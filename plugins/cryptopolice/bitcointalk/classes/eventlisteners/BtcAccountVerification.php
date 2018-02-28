<?php namespace CryptoPolice\Bitcointalk\Classes\EventListeners;

use DB;
use CryptoPolice\Bitcointalk\Models\Content;
class BtcAccountVerification
{
    public function handle()
    {
        $collection = Content::join('cryptopolice_bounty_user_registration', function ($query) {
            return $query
                ->on('cryptopolice_bounty_user_registration.btc_username', '=', 'cryptopolice_bitcointalk_contents.user_nick')
                ->where('btc_status', false);
        })->get([
            'cryptopolice_bitcointalk_contents.content',
            'cryptopolice_bitcointalk_contents.user_nick',
            'cryptopolice_bounty_user_registration.btc_code'
        ]);


        if($collection->isNotEmpty()){

            $collection = $this->verification($collection);
            $this->confirmation($collection);

        }
    }

    private function verification($collection)
    {
        foreach ($collection as $item) {

            if(preg_match('/' . $item->btc_code . '/', $item->content)) {
                yield $item;
            }
        }
    }

    private function confirmation($collection)
    {
        foreach ($collection as $item) {

            DB::table('cryptopolice_bounty_user_registration')
                ->where('btc_username', $item->user_nick)
                ->where('btc_code', $item->btc_code)
                ->update(['btc_status' => true]);
        }
    }
}