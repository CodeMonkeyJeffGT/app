<?php
namespace Api\Model;
use Think\Model;
class ImgModel extends Model {

    public function insPic($id, $pic)
    {
        $pId = $this->add(array(
            'u_id' => $id,
            'url' => $pic
        ));
        return array(
            'id' => $pId,
            'url' => $pic
        );
    }

    public function delOldPics()
    {

    }

}