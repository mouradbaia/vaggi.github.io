<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class InvoicesTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users');
    }

    public function successPayment($invoice = null)
    {
        if (!$invoice) {
            return false;
        }

        // Plans
        if ($invoice->type === 1) {
            $this->Users = TableRegistry::get('Users');

            $user = $this->Users->find()->contain(['Plans'])->where(['Users.id' => $invoice->user_id])->first();

            if ($invoice->status === 1) {
                $payment_period = unserialize($invoice->data)['payment_period'];
                $expiration = (new \Cake\I18n\Time($user->expiration))->addYear();
                if ($payment_period === 'm') {
                    $expiration = (new \Cake\I18n\Time($user->expiration))->addMonth();
                }
                $user->expiration = $expiration;
                $user->plan_id = $invoice->rel_id;

                $this->Users->save($user);
                if (isset($_SESSION['Auth']['User']['id'])) {
                    if ($_SESSION['Auth']['User']['id'] === $user->id) {
                        $data = $this->Users->find()->contain(['Plans'])
                            ->where(['Users.id' => $user->id])
                            ->first()
                            ->toArray();
                        unset($data['password']);
                        \Cake\Log\Log::write('debug', $data);
                        //$this->Auth->setUser($data);
                        $_SESSION['Auth']['User'] = $data;
                    }
                }
            }
        }

        // Campaigns
        if ($invoice->type === 2) {
            $this->Campaigns = TableRegistry::get('Campaigns');

            $campaign = $this->Campaigns->get($invoice->rel_id);

            if ($invoice->status === 1) {
                $campaign->status = 1;
                $this->Campaigns->save($campaign);
            } elseif ($invoice->status === 4) {
                $campaign->status = 7;
                $this->Campaigns->save($campaign);
            } elseif ($invoice->status === 5) {
                $campaign->status = 8;
                $this->Campaigns->save($campaign);
            }
        }

        // Wallet
        if ($invoice->type === 3) {
        }
        return true;
    }
}
