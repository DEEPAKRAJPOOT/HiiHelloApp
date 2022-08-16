<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Auth };
use App\Http\Requests\Api\Discovery\ { SetDiscoveryRequest };
use App\Http\Resources\v1\ { DiscoveryResource };
use App\Models\ { User, UserSetting, Location, Language };

class DiscoveryController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function setDiscoveryDetail(Request $request)
    {
        $setDiscoveryRequest = new SetDiscoveryRequest();
        if( $this->apiValidator($request->all(), $setDiscoveryRequest->rules()) ) {
            try{
                $user = $request->user();
                $location = Location::select('id')->whereCustomId($request->location)->whereIsActive('y')->firstOrFail();

                $user->discover_distance    =   $request->distance;
                $user->discover_start_age   =   $request->start_age;
                $user->discover_end_age     =   $request->end_age;
                $user->interest             =   $request->interest;
                $user->discover_location_id =   $location->id;
                $user->save();

                if(!empty($request->languages)){
                    $not_delete_interests = [];
                    $language_ids   =   Language::whereIn('lang_code',$request->languages)->whereIsActive('y')->pluck('id')->toArray();
                    foreach($language_ids as $language_id){
                        $custom_id = getUniqueString('user_settings');

                        UserSetting::updateOrCreate([
                            'user_id'       =>  $user->id,
                            'language_id'   =>  $language_id,
                        ],[
                            'custom_id'     =>  $custom_id,
                        ]);
                        $not_delete_interests[] = $custom_id;
                    }

                    // Delete Languages
                    UserSetting::whereUserId($user->id)->whereNotIn('custom_id',$not_delete_interests)->delete();
                }
                
                return (['data'  => NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.add', ['entity' => __('Discovery')]),
                    ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Location")]);
                        break;
                    case 'App\Models\Language':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Language")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'set_discovery');
            }
        }
        return $this->returnResponse();
    }

    public function getDiscoveryDetail(Request $request)
    {
        try{
            $user = User::select('id','custom_id','interest','discover_distance','discover_start_age',
                        'discover_end_age','discover_location_id')
                    ->with(['discoveryLocation:id,custom_id,is_active',
                        'discoveryLocation.locationTranslation:id,locale,location_id,name',
                        'userSettings:id,custom_id,user_id,language_id',
                        'userSettings.language:id,custom_id,language,lang_code,hint',
                    ])
                    ->whereId(Auth::id())->firstOrFail();
                            
            return (new DiscoveryResource($user))
                ->additional([
                    'meta' => [
                        'message'       =>  trans('api.list',['entity' => __("Discovery")]),
                    ] ]);
        } catch(ModelNotFoundException $exception) {                
            switch ($exception->getModel()) {
                case 'App\Models\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e,'get_discovery');
        }
        return $this->returnResponse();
    }
}
