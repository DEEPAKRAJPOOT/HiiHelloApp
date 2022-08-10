<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Redis;

trait RedisTrait {
	/* To check cache is enable or disable */
	public function cacheAllow(){ 
		return config('utility.api_caching'); 
	}

	/* To check the cache exist or not */
    public function cacheExist($redisKey){ 
        if($this->cacheAllow() && Redis::exists($redisKey) ) { return true; }
        return false;
    }

    /* To set the details in cache */
    public function setCache($redisKey, $value){
        return Redis::set($redisKey, json_encode($value)); 
    }

    /* To get the details from the cache */
    public function getCache($redisKey){
        return json_decode(Redis::get($redisKey));
    }
    
    /* To delete the details from the cache */
    public function deleteCache($redisKey){
    	return Redis::del($redisKey);
    }

    /*
	public function getRedisKey($key){
		$redis_keys = [
			'get-countries'	=>	'api_get_countries'
		];

		foreach($redis_keys as $api_name => $redis_key){
			if($api_name == $key){
				return $redis_key;
			}
		}

		return null;
	}
	*/
}
