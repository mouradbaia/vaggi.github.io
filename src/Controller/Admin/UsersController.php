<?php

namespace App\Controller\Admin;

use App\Controller\Admin\AppAdminController;
use Cake\Network\Exception\NotFoundException;
use Cake\I18n\Time;
use Cake\Cache\Cache;
use Cake\Mailer\MailerAwareTrait;
use Cake\Datasource\ConnectionManager;

class UsersController extends AppAdminController
{
    use MailerAwareTrait;

    public function dashboard()
    {
        $domains_auth_urls = [];
        $multi_domains = get_all_multi_domains_list();
        $main_domain = get_option('main_domain', '');
        unset($multi_domains[$main_domain]);

        if (isset($_SESSION['Auth']['User']['domains_auth']) &&
            $_SESSION['Auth']['User']['domains_auth'] == 'required' &&
            count($multi_domains)
        ) {
            $data = urlencode(data_encrypt([
                'session_name' => session_name(),
                'session_id' => session_id(),
                'time' => time()
            ]));

            foreach ($multi_domains as $key => $value) {
                $domains_auth_urls[] = '//' . $value . $this->request->base . '/auth/users/multidomains-auth' .
                    '?auth=' . $data;
            }
        }
        $this->set('domains_auth_urls', $domains_auth_urls);

        ///////////////////////////

        $last_record = $this->Users->Statistics->find()
            ->select('created')
            ->order(['created' => 'DESC'])
            ->first();

        if (!$last_record) {
            $last_record = Time::now();
        } else {
            $last_record = $last_record->created;
        }

        $first_record = $this->Users->Statistics->find()
            ->select('created')
            ->order(['created' => 'ASC'])
            ->first();

        if (!$first_record) {
            $first_record = Time::now()->modify('-1 second');
        } else {
            $first_record = $first_record->created;
        }

        $year_month = [];

        $last_month = Time::now()->year($last_record->year)->month($last_record->month)->startOfMonth();
        $first_month = Time::now()->year($first_record->year)->month($first_record->month)->startOfMonth();

        while ($first_month <= $last_month) {
            $year_month[$last_month->format('Y-m')] = $last_month->i18nFormat('LLLL Y');

            $last_month->modify('-1 month');
        }

        $this->set('year_month', $year_month);

        $to_month = Time::now()->format('Y-m');
        if (isset($this->request->query['month']) &&
            array_key_exists($this->request->query['month'], $year_month)
        ) {
            $to_month = explode('-', $this->request->query['month']);
            $year = (int)$to_month[0];
            $month = (int)$to_month[1];
        } else {
            $time = new Time($to_month);
            $current_time = $time->startOfMonth();

            $year = (int)$current_time->format('Y');
            $month = (int)$current_time->format('m');
        }

        $date1 = Time::now()->year($year)->month($month)->startOfMonth()->format('Y-m-d H:i:s');
        $date2 = Time::now()->year($year)->month($month)->endOfMonth()->format('Y-m-d H:i:s');

        $connection = ConnectionManager::get('default');

        $CurrentMonthDays = Cache::read('currentMonthDays_'.$date1.'_'.$date2, '15min');
        if ($CurrentMonthDays === false) {
            $sql = "SELECT 
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      DATE_FORMAT(Statistics.created, '%d-%m-%Y')
                  END  AS `day`,
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      COUNT(Statistics.id)
                  END AS `count`,
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      SUM(Statistics.owner_earn)
                  END AS `owner_earnings`,
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      SUM(Statistics.publisher_earn)
                  END AS `publisher_earnings`,
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      SUM(Statistics.referral_earn)
                  END AS `referral_earnings`
                FROM 
                  statistics Statistics 
                WHERE 
                  (
                    Statistics.created BETWEEN :date1 
                    AND :date2
                  ) 
                GROUP BY 
                  day";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('date1', $date1, 'datetime');
            $stmt->bindValue('date2', $date2, 'datetime');
            $stmt->execute();
            $views = $stmt->fetchAll('assoc');

            $CurrentMonthDays = [];

            $targetTime = Time::now();
            $targetTime->year($year)
                ->month($month)
                ->day(1);

            for ($i = 1; $i <= $targetTime->format('t'); $i++) {
                $CurrentMonthDays[$i . "-" . $month . "-" . $year] = [
                    'view' => 0,
                    'owner_earnings' => 0,
                    'publisher_earnings' => 0,
                    'referral_earnings' => 0,
                ];
            }
            foreach ($views as $view) {
                if (!$view['day']) {
                    continue;
                }
                $day = Time::now()->modify($view['day'])->format('j-n-Y');
                $CurrentMonthDays[$day]['view'] = $view['count'];
                $CurrentMonthDays[$day]['owner_earnings'] = $view['owner_earnings'];
                $CurrentMonthDays[$day]['publisher_earnings'] = $view['publisher_earnings'];
                $CurrentMonthDays[$day]['referral_earnings'] = $view['referral_earnings'];
            }

            Cache::write('currentMonthDays_'.$date1.'_'.$date2, $CurrentMonthDays, '5min');
        }
        $this->set('CurrentMonthDays', $CurrentMonthDays);

        $this->set('owner_earnings', array_sum(array_column($CurrentMonthDays, 'owner_earnings')));
        $this->set('publisher_earnings', array_sum(array_column($CurrentMonthDays, 'publisher_earnings')));
        $this->set('referral_earnings', array_sum(array_column($CurrentMonthDays, 'referral_earnings')));
        $this->set('total_views', array_sum(array_column($CurrentMonthDays, 'view')));

        /*
        if (($popularLinks = Cache::read('popularLinks_' . $date1 . '_' . $date2, '5min')) === false) {
            $popularLinks = $this->Users->Statistics->find()
                ->contain(['Links'])
                ->select([
                    'Links.alias',
                    'Links.url',
                    'Links.title',
                    'Links.domain',
                    'Links.created',
                    'views' => "count(case when Statistics.publisher_earn > 0 then Statistics.publisher_earn end)",
                    'publisher_earnings' => 'SUM(Statistics.publisher_earn)'
                ])
                ->where([
                    "Statistics.created BETWEEN :date1 AND :date2",
                ])
                ->order(['views' => 'DESC'])
                ->bind(':date1', $date1, 'datetime')
                ->bind(':date2', $date2, 'datetime')
                ->limit(10)
                ->group('Statistics.link_id')
                ->toArray();
            Cache::write('popularLinks_' . $date1 . '_' . $date2, $popularLinks, '5min');
        }

        $this->set('popularLinks', $popularLinks);
        */
    }

    public function index()
    {
        $conditions = [];

        $filter_fields = ['id', 'status', 'username', 'email', 'country', 'login_ip', 'register_ip', 'other_fields'];

        //Transform POST into GET
        if ($this->request->is(['post', 'put']) && isset($this->request->data['Filter'])) {
            $filter_url = [];

            $filter_url['controller'] = $this->request->params['controller'];

            $filter_url['action'] = $this->request->params['action'];

            // We need to overwrite the page every time we change the parameters
            $filter_url['page'] = 1;

            // for each filter we will add a GET parameter for the generated url
            foreach ($this->request->data['Filter'] as $name => $value) {
                if (in_array($name, $filter_fields) && $value) {
                    // You might want to sanitize the $value here
                    // or even do a urlencode to be sure
                    $filter_url[$name] = urlencode($value);
                }
            }
            // now that we have generated an url with GET parameters,
            // we'll redirect to that page
            return $this->redirect($filter_url);
        } else {
            // Inspect all the named parameters to apply the filters
            foreach ($this->request->query as $param_name => $value) {
                $value = urldecode($value);
                if (in_array($param_name, $filter_fields)) {
                    if (in_array($param_name, ['username', 'email'])) {
                        $conditions[] = [
                            ['Users.' . $param_name . ' LIKE' => '%' . $value . '%']
                        ];
                    } elseif (in_array($param_name, ['other_fields'])) {
                        $conditions['OR'] = [
                            ['Users.first_name LIKE' => '%' . $value . '%'],
                            ['Users.last_name LIKE' => '%' . $value . '%'],
                            ['Users.address1 LIKE' => '%' . $value . '%']
                        ];
                    } elseif (in_array($param_name, ['id', 'status', 'country', 'login_ip', 'register_ip'])) {
                        if ($param_name == 'status' && !in_array($value, [1, 2, 3])) {
                            continue;
                        }
                        $conditions['Users.' . $param_name] = $value;
                    }
                    $this->request->data['Filter'][$param_name] = $value;
                }
            }
        }

        $query = $this->Users->find()
            ->where($conditions)
            ->where(['Users.username <>' => 'anonymous']);
        $users = $this->paginate($query);
        $this->set('users', $users);
    }

    public function referrals()
    {
        $query = $this->Users->find()->where(['Users.referred_by >' => 0]);
        $referrals = $this->paginate($query);

        foreach ($referrals as $referral) {
            $referral->referred_by_username = $this->Users->get($referral->referred_by)->username;
        }

        $this->set('referrals', $referrals);
    }

    public function view($id = null)
    {
        if (!$id) {
            throw new NotFoundException(__('Invalid User'));
        }

        $user = $this->Users->find()
            ->contain(['Plans'])
            ->where([
                'Users.id' => $id
            ])
            ->first();
        if (!$user) {
            throw new NotFoundException(__('Invalid User'));
        }
        $this->set('user', $user);

        $total_links = $this->Users->Links->find()
            ->where([
                'user_id' => $id
            ])
            ->count();
        $total_links = display_price_currency($total_links, [
            'places' => 0,
            'before' => '',
            'after' => '',
        ]);
        $this->set('total_links', $total_links);

        $total_withdrawn = $this->Users->Withdraws->find()
            ->select(['total' => 'SUM(amount)'])
            ->where([
                'user_id' => $id,
                'status' => 3
            ])
            ->first();
        $this->set('total_withdrawn', $total_withdrawn->total);

        $pending_withdrawn = $this->Users->Withdraws->find()
            ->select(['total' => 'SUM(amount)'])
            ->where([
                'user_id' => $id,
                'status' => 2
            ])
            ->first();
        $this->set('pending_withdrawn', $pending_withdrawn->total);

        $referrals = $this->Users->find()
            ->select(['username', 'created'])
            ->where(['referred_by' => $id]);
        $this->set('referrals', $referrals);
    }

    public function add()
    {
        $user = $this->Users->newEntity();

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);

            $user->api_token = \Cake\Utility\Security::hash(\Cake\Utility\Text::uuid(), 'sha1', true);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been added.'));
                return $this->redirect(['action' => 'view', $user->id]);
            }
            $this->Flash->error(__('Unable to add the user.'));
        }
        $this->set('user', $user);
    }

    public function message($id = null)
    {
        if (!$id) {
            throw new NotFoundException(__('Invalid User'));
        }

        $user = $this->Users->findById($id)->first();
        if (!$user) {
            throw new NotFoundException(__('Invalid User'));
        }
        $this->set('user', $user);

        $message = new \App\Form\MessageUserForm();

        if ($this->request->is('post')) {
            if ($message->execute($this->request->data)) {
                $this->Flash->success('Your message has been delivered.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('There was a problem submitting your form.');
            }
        }
        $this->set('message', $message);
    }

    public function resendActivation($id = null)
    {
        if (!$id) {
            throw new NotFoundException(__('Invalid User'));
        }

        $user = $this->Users->findById($id)->first();
        if (!$user) {
            throw new NotFoundException(__('Invalid User'));
        }
        $this->set('user', $user);

        $user->activation_key = \Cake\Utility\Security::hash(\Cake\Utility\Text::uuid(), 'sha1', true);

        if ($this->Users->save($user)) {
            $this->getMailer('User')->send('activation', [$user]);
            $this->Flash->success(__('The activation email has been sent, Please ask your user to check email ' .
                'inbox or spam folder to activate his account.'));
            return $this->redirect($this->referer());
        }

        $this->Flash->error(__('Unable to add the user.'));
        return $this->redirect($this->referer());
    }

    public function edit($id = null)
    {
        if (!$id) {
            throw new NotFoundException(__('Invalid User'));
        }

        $user = $this->Users->findById($id)->where(['Users.username <>' => 'anonymous'])->first();
        if (!$user) {
            throw new NotFoundException(__('Invalid User'));
        }

        $plans = $this->Users->Plans->find('list', [
            'keyField' => 'id',
            'valueField' => 'title'
        ])
            ->where(['enable' => 1]);

        $this->set('plans', $plans);

        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to edit user.'));
        }
        $this->set('user', $user);
    }

    public function deactivate($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $user = $this->Users->findById($id)->where(['Users.username <>' => 'anonymous'])->first();

        $user->status = 3;

        if ($this->Users->save($user)) {
            $this->Flash->success(__('The Link with id: {0} has been deactivated.', $user->id));
            return $this->redirect(['action' => 'index']);
        }
    }
}
