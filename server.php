<?php

include_once("vendor/autoload.php");

use Liberty\WebPeer;
use Liberty\Blockchain;
use Liberty\Transaction;




// Configuration

// The ini file of the default wallet (required for fee distribution)
$wallet = "wallet.ini";

// Blockchain Directory
$block = "blocks";









if( isset($_REQUEST['ping']) ) {
	
	$json["Main"] = "Available";
	$json["Date"] = date("Y-m-d h:i:s");
	$json["Time"] = time();

	//header('Content-type: text/plain; charset=utf-8');
	header('Content-type: application/json; charset=utf-8');
	echo json_encode($json, JSON_FORCE_OBJECT);

	exit;
}





if( isset($_REQUEST['connect']) ) {

	$json = array();
	
	try {
		$peer = new Liberty\WebPeer($block);
	} catch(Exception $e) {
		echo $e->getMessage();
		exit;
	}
	
	// Broadcast
	$peer->broadcast(Liberty\Wallet::addressFee($wallet));

	// Remote
	$peer->peerUpdate();
	
	// JSON response
	$json["Peer"] = $peer->peerList();
	$json["Address"] = $peer->peerAddress();
	$json["Main"] = "connect";

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($json, JSON_FORCE_OBJECT);
	
	exit;
}





if( isset($_REQUEST['peers']) ) {
	$json = array();
	
	try {
		$peer = new Liberty\WebPeer($block);
	} catch(Exception $e) {
		echo $e->getMessage();
		exit;
	}
	
	$json["Peer"] = $peer->peerList();
	$json["Address"] = $peer->peerAddress();
	$json["Main"] = "peers";

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($json, JSON_FORCE_OBJECT);
	
	exit;
}




if( isset($_REQUEST['blockheight']) ) {

	try{
		$bc = new Blockchain($block);

		$json["Main"] = "peers";
		$json["LastBlock"] = $bc->transactions - 1;

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json, JSON_FORCE_OBJECT);

	} catch(Exception $e) {
		echo $e->getMessage();
	}
	
	exit;
}




if( isset($_REQUEST['blocks']) ) {

	if(!isset($_REQUEST['start'])) {
		$_REQUEST['start'] = 0;
	}

	if(!isset($_REQUEST['limit'])) {
		$_REQUEST['limit'] = 0;
	}

	try{
		$bc = new Blockchain($block);

		$lines = $bc->blockPack($_REQUEST['start'], $_REQUEST['limit']);

		for($a=0; $a<count($lines); $a++) {
			$json["$a"] = $lines[$a];
		}

		$json["Main"] = "blocks";
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json, JSON_FORCE_OBJECT);

	} catch(Exception $e) {
		echo $e->getMessage();
	}

	exit;
}





if( isset($_REQUEST['sync']) ) {

	try {
		$wp = new WebPeer($block);
		if($wp->peerNode() == $_REQUEST['url']) exit;

	} catch(Exception $e) {
		echo $e->getMessage();
	}

	
	try{
		$bc = new Blockchain($block);
		$bc->sync($_REQUEST['url']);

		$json["Main"] = "sync";
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json, JSON_FORCE_OBJECT);

	} catch(Exception $e) {
		echo $e->getMessage();
	}

	exit;
}




if( isset($_REQUEST['transaction']) ) {

	try {
		$tx = new Transaction($block);
		$receiver = $tx->receiver($_REQUEST['message'], $_REQUEST['signature']);

		$json["Main"] = "transaction";
		$json["Tx"] = $receiver;
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json, JSON_FORCE_OBJECT);

	} catch(Exception $e) {
		echo $e->getMessage();
	}

	exit;
}




if( isset($_REQUEST['utxs']) ) {

	try {
		$tx = new Transaction($block);

		$txs = $tx->block();

		for($a=0; $a<count($lines); $a++) {
			$json["$a"] = $txs[$a];
		}

		$json["Main"] = "utxs";
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json, JSON_FORCE_OBJECT);

	} catch(Exception $e) {
		echo $e->getMessage();
	}

	exit;
}





if( isset($_REQUEST['test']) ) {


	exit;
}



echo "<h1>Liberty Server is UP!</h1><br/>";





?>