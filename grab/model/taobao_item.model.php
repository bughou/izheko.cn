<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/../common/model/taobao_api.model.php';
require_once APP_ROOT . '/../common/helper/json.helper.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';

class TaobaoItem
{
    static function get_item_info($num_iid)
    {
        if($result = TaobaoApi::item_get($num_iid))
        {
            if(isset($result['item_get_response']['item']))
                $item_info = $result['item_get_response']['item'];
            elseif(
                isset($result['error_response']['sub_msg']) &&
                ( ($sub_msg = $result['error_response']['sub_msg']) === '该商品已被删除'
                || $sub_msg === '未登录用户不能获取小二下架或删除的商品')
            ) return 'deleted';
            else return;
        }
        else return;

        if(isset($item_info['price']))
            $item_info['price'] = parse_price($item_info['price']);
        if(is_array($promo_info = self::get_promo_info($num_iid)) &&
            $promo_info['promo_price'] < $item_info['price']
        )
        $item_info = array_merge($item_info, $promo_info);

        return $item_info;
    }

    static function get_promo_info($num_iid)
    {
        if(!$price_info = self::get_price_info($num_iid)) return;
        $promo = null;
        foreach ($price_info as $sku)
        {
            if (isset($sku['promotionList']) &&
                is_array($promo_list = $sku['promotionList'])
            )
            foreach($promo_list as $this_promo)
            {
                if (is_array($this_promo) && isset($this_promo['price']) &&
                    ($price = parse_price($this_promo['price'])) &&
                    (is_null($promo) || $price < $promo['price'])
                ){
                    $this_promo['price'] = $price;
                    $promo = $this_promo;
                }
            }
        }
        if(is_int($promo['startTime']))
            $promo['startTime'] = strftime('%F %T', $promo['startTime'] / 1000);
        if(is_int($promo['endTime']))
            $promo['endTime']   = strftime('%F %T', $promo['endTime']   / 1000);
        return array(
            'promo_price' => $promo['price'],
            'promo_start' => $promo['startTime'],
            'promo_end'   => $promo['endTime'],
            'promo_vip'   => isset($promo['type']) && ($promo['type'] === 'VIP价格' || $promo['type'] === '店铺vip'),
        );
    }

    static function get_price_info($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $refer='http://detail.tmall.com/item.htm?id=' . $num_iid;
        $url='http://mdskip.taobao.com/core/initItemDetail.htm?queryMaybach=true&itemId=' . $num_iid;
        $response = $curl->get($url, $refer);

        $data = decode_json(iconv('GBK', 'UTF-8', $response->body));
        if ( isset($data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            ($price_info = $data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            is_array($price_info)
        ) return $price_info;
    }

    static function get_promo_info2($num_iid)
    {
        if (($result = TaobaoApi::ump_promotion_get($num_iid)) &&
            isset($result['ump_promotion_get_response']['promotions']
            ['promotion_in_item']['promotion_in_item'][0]) &&
            ($promo_info = $result['ump_promotion_get_response']['promotions']
            ['promotion_in_item']['promotion_in_item'][0])
        )
        {
            $item_info['promo_price'] = parse_price($promo_info['item_promo_price']);
            $item_info['promo_start'] = $promo_info['start_time'];
            $item_info['promo_end']   = $promo_info['end_time'];
        }
    }

    static function get_vip_price2($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $refer = 'http://item.taobao.com/item.htm?id=' . $num_iid;
        $url   = 'http://ajax.tbcdn.cn/json/umpStock.htm?itemId=' . $num_iid . '&p=1&rcid=28&sts=341317634,1170940438677291012,1225260582244974720,1166502676530463747&chnl=pc&price=9990&sellerId=10142375&shopId=&cna=M5sSCm8Hwi0CAbaW6nJssBNK&ref=&buyerId=174294739&nick=bughou&tg=1316864&tg2=67108872&tg3=1224979098644774912&tg4=4573968373776384&tg6=0';
        $response = $curl->get($url, $refer);
        if (! preg_match('/TB\.PromoData\s*=\s*({.*})/s', $response->body, $matches))
        {
            error_log('unexpected response:' . $response->body);
            return;
        }
        $data = decode_json(iconv('GBK', 'UTF-8', $matches[1]));
        $vip_price = null;
        if (is_array($data)) foreach($data as $sku)
            if (is_array($sku)) foreach($sku as $promo)
                if (is_array($promo) &&
                    isset($promo['type']) && $promo['type'] === 'VIP价格' &&
                    isset($promo['price']) && ($price = parse_price($promo['price'])) &&
                    (is_null($vip_price) || $price < $vip_price)
                ) $vip_price = $price;
        return $vip_price;
    }
}


