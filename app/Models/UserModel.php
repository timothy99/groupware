<?php namespace App\Models;

use CodeIgniter\Model;
use Throwable;
use stdClass;

class UserModel extends Model
{
    // 사용자 정보 입력
    public function insertUserInfo($data)
    {
        $user_name = $data["user_name"];
        $user_id = $data["user_id"];
        $user_password = $data["user_password"];

        $result = true;
        $message = "입력이 잘 되었습니다";
        $insert_id = 99;

        try {
            $db = \Config\Database::connect();

            $db->transStart();

            $builder = $db->table("gwt_user");
            $builder->set("user_id", $user_id);
            $builder->set("user_name", $user_name);
            $builder->set("user_password", $user_password);
            $builder->set("admin_yn", "N");
            $builder->set("use_yn", "Y");
            $builder->set("del_yn", "N");
            $builder->set("ins_id", $user_id);
            $builder->set("ins_date", "now()", false);
            $builder->set("upd_id", $user_id);
            $builder->set("upd_date", "now()", false);
            $result = $builder->insert();
            $insert_id = $db->insertID();

            $db->transComplete();
        } catch (Throwable $t) {
            $result = false;
            $message = "입력에 오류가 발생했습니다.";
            logMessage($t->getMessage());
        }

        $model_result = array();
        $model_result["result"] = $result;
        $model_result["message"] = $message;
        $model_result["insert_id"] = $insert_id;

        return $model_result;
    }

    // 로그인 할때 사용자가 맞는지 정보 갖고 오기
    public function getLoginInfo($data)
    {
        $db_result = true;
        $db_message = "조회에 성공했습니다.";
        $db_info = new stdClass();

        $user_id = $data["user_id"];
        $user_password = $data["user_password"];

        try {
            $db = \Config\Database::connect();

            $builder = $db->table("gwt_user");
            $builder->select("user_idx");
            $builder->select("user_id");
            $builder->select("admin_yn");
            $builder->select("count(*) as cnt");
            $builder->where("user_id", $user_id);
            $builder->where("user_password", $user_password);
            $builder->where("use_yn", "Y");
            $builder->where("del_yn", "N");
            $db_info = $builder->get()->getFirstRow(); // 쿼리 실행
        } catch (Throwable $t) {
            $db_result = false;
            $db_message = "조회에 오류가 발생했습니다.";
        }

        $model_result = array();
        $model_result["result"] = $db_result;
        $model_result["message"] = $db_message;
        $model_result["db_info"] = $db_info;

        return $model_result;
    }

    // 사용자 아이디 중복체크
    public function getUserIdCheck($user_id)
    {
        $db_result = true;
        $db_message = "조회에 성공했습니다.";
        $db_info = new stdClass();

        try {
            $db = \Config\Database::connect();

            $builder = $db->table("gwt_user");
            $builder->select("count(*) as cnt");
            $builder->where("user_id", $user_id);
            $builder->where("use_yn", "Y");
            $builder->where("del_yn", "N");
            $db_info = $builder->get()->getFirstRow(); // 쿼리 실행

            // 아이디가 중복된 경우
            $cnt = $db_info->cnt;
            if ($cnt > 0) {
                $db_result = false;
                $db_message = "중복된 아이디입니다. 다른 아이디를 입력해주세요.";
            }
        } catch (Throwable $t) {
            $db_result = false;
            $db_message = "조회에 오류가 발생했습니다.";
        }

        $model_result = array();
        $model_result["result"] = $db_result;
        $model_result["message"] = $db_message;
        $model_result["db_info"] = $db_info;

        return $model_result;
    }

}
