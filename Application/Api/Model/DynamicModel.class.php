<?php
namespace Api\Model;
use Think\Model;
class DynamicModel extends Model {

    public function listDynamics($last_id, $limit = 20, $u_id = 0)
    {
        $offset = 0;
        $hasMore = 1;
        $dynamics = array();

        $now = time();
        $nowDay = (int)(strtotime(date('Y-m-d 00:00:00', time())) / 86400);

        $sql = '
            SELECT `d`.`id` `id`, `user`.`head_img_url` `head_img_url`, `user`.`nickname` `nickname`, `d`.`content`, `img`.`id` `img_id`, `img`.`url` `img_url`, `d`.`pub_time` `pub_time`, `comment_num`.`num` `comment_num`, `dynamic_like_num`.`num` `like_num`, `is_like_tab`.`is` `is_like`
            FROM `dynamic` `d`
            LEFT JOIN `user` ON `user`.`id` = `d`.`u_id`
            LEFT JOIN `img` ON `d`.`id` = `img`.`d_id`
            LEFT JOIN (
                SELECT `d_id`, count(`id`) `num`
                FROM `comment`
                GROUP BY `d_id`
            ) `comment_num` ON `d`.`id` = `comment_num`.`d_id`
            LEFT JOIN (
                SELECT `d_id`, count(`id`) `num`
                FROM `dynamic_like`
                GROUP BY `d_id`
            ) `dynamic_like_num` ON `d`.`id` = `dynamic_like_num`.`d_id`
            LEFT JOIN (
                SELECT `id` `is`, `d_id`
                FROM `dynamic_like`
                WHERE `u_id` = %d
            ) is_like_tab ON `d`.`id` = `is_like_tab`.`d_id`
            WHERE `d`.`u_id` <> 0' . ($last_id == 0 ? '' : ' AND `d`.`id` < %d AND `d`.`id` > %d') . '
            ORDER BY `id` DESC
            ' . ($last_id == 0 ? 'LIMIT %d' : '') . '
        ';

        do{

            $last_count = count($dynamics);

            if($last_id == 0)
                $sqlData = $this->query($sql, $u_id, $limit * 6);
            else
                $sqlData = $this->query($sql, $u_id, $last_id, $last_id - $limit * 1.5);
            $sqlData = line_to_up($sqlData);

            $tmpDy = $sqlData[0];
            if(empty($tmpDy['imgId']))
                $tmpDy['img'] = array();
            else
            {
                $tmpDy['img'] = array(array(
                    'id' => $tmpDy['imgId'],
                    'url' => $tmpDy['imgUrl']
                ));
            }
            unset($tmpDy['imgId']);
            unset($tmpDy['imgUrl']);
            for($i = 1, $len = count($sqlData); $i < $len; $i++)
            {
                if($tmpDy['id'] != $sqlData[$i]['id'])
                {
                    $dynamics[] = $tmpDy;
                    $tmpDy = $sqlData[$i];
                    if(empty($tmpDy['imgId']))
                        $tmpDy['img'] = array();
                    else
                    {
                        $tmpDy['img'] = array(array(
                            'id' => $tmpDy['imgId'],
                            'url' => $tmpDy['imgUrl']
                        ));
                    }
                    unset($tmpDy['imgId']);
                    unset($tmpDy['imgUrl']);
                }
                else
                {
                    $tmpDy['img'][] = array(
                        'id' => $sqlData[$i]['imgId'],
                        'url' => $sqlData[$i]['imgUrl']
                    );
                }
            }
            if( ! empty($tmpDy))
                $dynamics[] = $tmpDy;

            $dynamics = array_slice($dynamics, 0, -1);

            if(empty($dynamics))
                break;

            $offset = $dynamics[count($dynamics) - 1]['id'];

        }while(count($dynamics) <= $limit && $last_count == count($dynamics));

        if(count($dynamics) > $limit)
        {
            $dynamics = array_slice($dynamics, 0, -1);
            $dynamics = array_slice($dynamics, 0, $limit);
            $offset = $dynamics[$limit - 1]['id'];
        }
        else
        {
            $hasMore = 0;
        }

        for($i = 0, $len = count($dynamics); $i < $len; $i++)
        {
            $tmpTime = $dynamics[$i]['pubTime'];
            $tmpPubTime = date('Y-m-d H:i:s', $tmpTime);
            if($now - $tmpTime < 60)
            {
                $tmpPubTime = '刚刚';
            }
            else if($now - $tmpTime < 3600)
            {
                $tmpPubTime = floor(($now - $tmpTime) / 60) . '分钟前';
            }
            else if((int)(strtotime(date('Y-m-d 00:00:00', $tmpTime)) / 86400) == $nowDay)
            {
                $tmpPubTime = floor(($now - $tmpTime) / 3600) . '小时前';
            }
            else if($nowDay - (int)(strtotime(date('Y-m-d 00:00:00', $tmpTime)) / 86400)  == 1)
            {
                $tmpPubTime = '昨天 ' . date('H:i', $tmpTime);
            }
            else if(date('Y', $tmpTime) == date('Y'))
            {
                $tmpPubTime = date('m-d H:i', $tmpTime);
            }
            else
            {
                $tmpPubTime = substr($tmpPubTime, 2);
            }
            $dynamics[$i]['pubTime'] = $tmpPubTime;

            $dynamics[$i]['content'] = base64_decode($dynamics[$i]['content']);
            $dynamics[$i]['brief'] = mb_substr($dynamics[$i]['content'], 0, 100);
            $dynamics[$i]['isWhole'] = ($dynamics[$i]['brief'] == $dynamics[$i]['content']) ? 1 : 0;
            unset($dynamics[$i]['content']);

            $dynamics[$i]['isLike'] = empty($dynamics[$i]['isLike']) ? 0 : 1;
        }

        return array(
            'dynamics' => $dynamics,
            'offset'   => $offset,
            'hasMore'  => $hasMore
        );
    }

    // public function listFollowDynamics($id, $last_id, $limit = 20)
    // {
    //     return $this->where('id < %d AND u_id = %d', $last_id,  $id)
    //         ->order('id DESC')
    //         ->limit((int)$limit)
    //         ->select();
    // }
     
    public function getDynamic($id, $u_id = 0)
    {
        $sql = '
            SELECT `d`.`id` `id`, `user`.`head_img_url` `head_img_url`, `user`.`nickname` `nickname`, `d`.`content`, `img`.`id` `img_id`, `img`.`url` `img_url`, `d`.`pub_time` `pub_time`, `comment_num`.`num` `comment_num`, `dynamic_like_num`.`num` `like_num`, `is_like_tab`.`is` `is_like`
            FROM `dynamic` `d`
            LEFT JOIN `user` ON `user`.`id` = `d`.`u_id`
            LEFT JOIN `img` ON `d`.`id` = `img`.`d_id`
            LEFT JOIN (
                SELECT `d_id`, count(`id`) `num`
                FROM `comment`
                GROUP BY `d_id`
            ) `comment_num` ON `d`.`id` = `comment_num`.`d_id`
            LEFT JOIN (
                SELECT `d_id`, count(`id`) `num`
                FROM `dynamic_like`
                GROUP BY `d_id`
            ) `dynamic_like_num` ON `d`.`id` = `dynamic_like_num`.`d_id`
            LEFT JOIN (
                SELECT `id` `is`, `d_id`
                FROM `dynamic_like`
                WHERE `u_id` = %d
            ) is_like_tab ON `d`.`id` = `is_like_tab`.`d_id`
            WHERE `d`.`id` = %d;
        ';
        $sqlData = $this->query($sql, $u_id, $id);
        if(count($sqlData) == 0)
            return 0;

        $sqlData = line_to_up($sqlData);

        $dynamic = $sqlData[0];
        unset($dynamic['imgId']);
        unset($dynamic['imgUrl']);
        $dynamic['img'] = array();
        for($i = 0, $len = count($sqlData); $i < $len; $i++)
        {
            if( ! empty($sqlData[$i]['imgId']))
                $dynamic['img'][] = array(
                    'id' => $sqlData[$i]['imgId'],
                    'url' => $sqlData[$i]['imgUrl']
                );
        }

        $now = time();
        $nowDay = (int)(strtotime(date('Y-m-d 00:00:00', time())) / 86400);
        $tmpTime = $dynamic['pubTime'];
        $tmpPubTime = date('Y-m-d H:i:s', $tmpTime);
        if($now - $tmpTime < 60)
        {
            $tmpPubTime = '刚刚';
        }
        else if($now - $tmpTime < 3600)
        {
            $tmpPubTime = floor(($now - $tmpTime) / 60) . '分钟前';
        }
        else if((int)(strtotime(date('Y-m-d 00:00:00', $tmpTime)) / 86400) == $nowDay)
        {
            $tmpPubTime = floor(($now - $tmpTime) / 3600) . '小时前';
        }
        else if($nowDay - (int)(strtotime(date('Y-m-d 00:00:00', $tmpTime)) / 86400)  == 1)
        {
            $tmpPubTime = '昨天 ' . date('H:i', $tmpTime);
        }
        else if(date('Y', $tmpTime) == date('Y'))
        {
            $tmpPubTime = date('m-d H:i', $tmpTime);
        }
        else
        {
            $tmpPubTime = substr($tmpPubTime, 2);
        }
        $dynamic['pubTime'] = $tmpPubTime;
        $dynamic['isLike'] = empty($dynamic['isLike']) ? 0 : 1;
        $dynamic['content'] = base64_decode($dynamic['content']);

        return $dynamic;
    }

}