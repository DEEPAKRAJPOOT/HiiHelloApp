<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\TwilioTrait;
use App\Models\User;
use App\Models\TwilioSubaccount;

class UserCommunication extends Model
{
    use HasFactory, TwilioTrait;
    protected $fillable = ['user_id', 'subaccount_id', 'phone', 'identity']; 

    public function user(){ return $this->belongsTo('App\Models\User'); }
    public function subAccount(){ return $this->belongsTo('App\Models\TwilioSubaccount'); }

    public static function connectWithTwilio($user_id)
    {
        $account_user = $card_user = User::with(['subAccount','userCommunication', 'userDetails'])
                                    ->whereCustomId($user_id)->firstOrFail();
            
        if( empty($account_user->subAccount) ) {
            $account = TwilioTrait::createSubAccount($account_user->full_name);
            if($account){ 
                if(!empty($account->sid)){
                    $subAccount = TwilioSubaccount::create([
                        'user_id'   =>  $account_user->id,
                        'sid'       =>  $account->sid,
                        'token'     =>  $account->authToken
                    ]);

                    if($subAccount){
                        $subaccount_id      =   $subAccount->id;
                        $subaccount_sid     =   $subAccount->sid;
                        $subaccount_token   =   $subAccount->token;
                    }
                }
            }
        }else{
            $subaccount_id      =   $account_user->subAccount->id;
            $subaccount_sid     =   $account_user->subAccount->sid;
            $subaccount_token   =   $account_user->subAccount->token;
        }


        // Create Subaccount api key and secret
        if(empty($account_user->subAccount->api_key)){
            $api = TwilioTrait::createSubaccountApiKey($subaccount_sid, $subaccount_token);
            if(!empty($api->sid)){
                $subAccount = TwilioSubaccount::whereUserId($account_user->id)->update([
                    'api_key'       =>  $api->sid,
                    'api_secret'    =>  $api->secret
                ]);
            }
        }      

        $areaCode = '382405';
        // $billingAddressArr = (object)$account_user->details->billing_address;
      
        // if(!empty($billingAddressArr) && !empty($billingAddressArr->areacode)){
        //     $areaCode = $billingAddressArr->areacode;
        // }    

        if(empty($card_user->userCommunication->phone)){
            \Log::info( "Searching number for areacode ".$areaCode);   
            // $incomingNumber = TwilioTrait::searchIncomingNumber($areaCode);
                
            $incomingNumber = '+919909977985';
            if($incomingNumber){
                \Log::info( "Number found ".$incomingNumber);   
                $bought = TwilioTrait::buyIncomingNumber($incomingNumber, $subaccount_sid);
                if($bought){
                    \Log::info( "Number purchased ".$incomingNumber);   
                    $identity = strtolower(str_replace(" ","_", $card_user->full_name))."_".$card_user->id."_".date("YmdHis");        
                    self::create([
                        'user_id'           => $card_user->id, //user id of user
                        'subaccount_id'     => $subaccount_id,
                        'phone'             => $incomingNumber, 
                        'identity'          => $identity
                    ]);                   
                    $card_user->load('userCommunication');
                    // $card_user->userCommunication->phone = $incomingNumber;

                }else{
                    \Log::info( "Unable to purchase the number ".$incomingNumber);   
                    return $bought;
                }
            }else{
                \Log::info( "Number not found ".$incomingNumber);   
            }
        }

        //Create subAccount app SID
        if(empty($account_user->subAccount->app_sid)){
            $app = TwilioTrait::createSubAccountApplication("TwiML App for Sub Account", $subaccount_sid, $subaccount_token);
            if(!empty($app->sid)){
                TwilioSubaccount::whereUserId($account_user->id)->update([
                    'app_sid' => $app->sid
                ]);
            }
        }

        //Create subAccount IOS Push Token
        // if(empty($account_user->subAccount->ios_push_token)){
        //     $credential = TwilioTrait::createCredential($subaccount_sid, $subaccount_token, "APN", [
        //             "friendlyName"  =>  "iOS Push Credential",
        //             "certificate"   =>  file_get_contents(base_path("certificates/apn_certificate.txt")),
        //             "privateKey"    =>  file_get_contents(base_path("certificates/apn_private_key.txt"))
        //         ]);

        //     if(!empty($credential->sid)){
        //         TwilioSubaccount::whereUserId($account_user->id)->update([
        //             'ios_push_token' => $credential->sid
        //         ]);
        //     }
        // }


        // //Create subAccount Android Push Token
        // if(empty($account_user->subAccount->android_push_token)){
        //     $credential = TwilioTrait::createCredential($subaccount_sid, $subaccount_token, "FCM",[
        //         "friendlyName"  =>  "Android Push Credential",
        //         "secret"        =>  file_get_contents(base_path("certificates/fcm_server_key.txt"))
        //     ]);

        //     if(!empty($credential->sid)){
        //         TwilioSubaccount::whereUserId($account_user->id)->update([
        //             'android_push_token' => $credential->sid
        //         ]);
        //     }
        // }

        return true;        
    }
}
