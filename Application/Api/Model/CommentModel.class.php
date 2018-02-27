<?php
namespace Api\Model;
use Think\Model;
class CommentModel extends Model {

    public function listComment($d_id, $u_id = 0)
    {
        $sql = '
            SELECT `c`.`id` `id`, `user`.`head_img_url` `head_img_url`, `user`.`nickname` `nickname`, `c`.`content` `content`, `c`.`pub_time` `pub_time`, `p`.`u_id` `p_c_u_id`, `p`.`nickname` `p_c_nickname`, `comment_like_num`.`num` `like_num`, `is_like_tab`.`is` `is_like`
            FROM `comment` `c`
            LEFT JOIN (
                SELECT `comment`.`id` `id`, `user`.`id` `u_id`, `user`.`nickname` `nickname`
                FROM `comment`
                LEFT JOIN `user` ON `user`.`id` = `comment`.`u_id`
                WHERE `d_id` = %d AND `comment`.`u_id` <> 0
            ) `p` ON `p`.`id` = `c`.`p_id`
            LEFT JOIN `user` ON `user`.`id` = `c`.`u_id`
            LEFT JOIN (
                SELECT `c_id`, count(`id`) `num`
                FROM `comment_like`
                GROUP BY `c_id`
            ) `comment_like_num` ON `c`.`id` = `comment_like_num`.`c_id`
            LEFT JOIN (
                SELECT `id` `is`, `c_id`
                FROM `comment_like`
                WHERE `u_id` = %d
            ) is_like_tab ON `c`.`id` = `is_like_tab`.`c_id`
            WHERE `d_id` = %d AND `c`.`u_id` <> 0
            ORDER BY `c`.`pub_time` DESC
        ';
        $comments = $this->query($sql, $d_id, $u_id, $d_id);
        $comments = line_to_up($comments);
        for($i = 0, $len = count($comments); $i < $len; $i++)
        {
            $comments[$i]['isLike'] = empty($comments[$i]['isLike']) ? 0 : 1;
            $comments[$i]['pubTime'] = date('m-d H:i', $comments[$i]['pubTime']);
        }
        return $comments;
    }

    public function getComment($id)
    {
    	return $this->where('id = %d', $id)
    		->find();
    }

}