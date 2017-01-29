<?php

try {

	// 初期設定 共通部（API用のものを流用）
	$require_path = "./";
	include('lib/apifirstset.php');

	// ポストデータ取得関数
	require_once("class/getPost.php");
	$PD = new getPost($_POST);

	require_once("class/dao/MyDAO.class.php");
	require_once("class/dao/AccountDAO.class.php");


	session_start();

	// alertの取得
	$alert = array();
	if (isset($_SESSION["page_alert"])) {
		$alert = $_SESSION["page_alert"];
	}
	$smarty->assign("alertlist", $alert);

	// 使用しているセッション変数を全て解除する
	$_SESSION = array();

	$msg = "";

	if($PD->getVar("mode") == "exec") {

		$login_dao = new AccountDAO($dao);

		if ($PD->getVar("user") == "" || $PD->getVar("pass") == "") {
			$msg = $login_dao::LOGIN_ERR0;

		} else {

			$msg = $login_dao->loginCheck($PD->getVar("user"), $PD->getVar("pass"));

			if (is_null($msg)) {

				// ログイン成功だーーー
				session_regenerate_id(true);	// セッションIDを新規に発行する

				$_SESSION["user"] = $PD->getVar("user");

				header( "Location: admin/admTimeSchedule.php" ) ;

			}
		}

	} else {

		// セッションを切断するにはセッションクッキーも削除する。
		// Note: セッション情報だけでなくセッションを破壊する。
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
					$params["path"], $params["domain"],
					$params["secure"], $params["httponly"]
					);
		}

		// 最終的に、セッションを破壊する
		session_destroy();

	}

	$smarty->assign("user", $user = $PD->getVar("user"));
	$smarty->assign("msg", $msg);

	// htmlの表示
	$smarty->display("login.tpl");

	exit();


} catch(MyException $e){

	$root_path = "./";
	include('lib/exceptionset.php');

	exit();


}catch(Exception $e){

	$root_path = "./";
	include('lib/exceptionset.php');

	exit();

}


?>
