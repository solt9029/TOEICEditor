<?php
error_reporting(0);
require_once("./fpdf/mbfpdf.php");

// Ajax以外からのアクセスを遮断
$request="";
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"])){
	$request=strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]);
}
if($request!=="xmlhttprequest"){
	header("location: ./");
	exit;
}

if(isset($_POST["questions"]) && isset($_POST["A"]) && isset($_POST["B"]) && isset($_POST["C"]) && isset($_POST["D"])){
	$time=time();

	for($i=0; $i<count($_POST["questions"]); $i++){
		if(strlen(h($_POST["questions"][$i]))!=mb_strlen(h($_POST["questions"][$i]),"utf-8") || strlen(h($_POST["A"][$i]))!=mb_strlen(h($_POST["A"][$i]),'utf8') || strlen(h($_POST["B"][$i]))!=mb_strlen(h($_POST["B"][$i]),'utf8') || strlen(h($_POST["C"][$i]))!=mb_strlen(h($_POST["C"][$i]),'utf8') || strlen(h($_POST["D"][$i]))!=mb_strlen(h($_POST["D"][$i]),'utf8')){
			echo "半角英数のみ使用可です。";
			exit;
		}else if(strlen(h($_POST["questions"][$i]))>120 || strlen(h($_POST["A"][$i]))>35 || strlen(h($_POST["B"][$i]))>35 || strlen(h($_POST["C"][$i]))>35 || strlen(h($_POST["D"][$i]))>35){
			echo "長すぎる質問や選択肢が含まれています。";
			exit;
		}
	}

	$render_pdf_files=glob(__DIR__."/../pdf/render/*.pdf");
	if(count($render_pdf_files)>=50){
		sort($render_pdf_files);
		$delete_num=3;
		for($i=0; $i<$delete_num; $i++){
			unlink($render_pdf_files[$i]);
		}
	}

	$tweet_pdf_files=glob(__DIR__."/../pdf/tweet/*.pdf");
	if(count($tweet_pdf_files)>=300){
		sort($tweet_pdf_files);
		$delete_num=1;
		for($i=0; $i<$delete_num; $i++){
			unlink($tweet_pdf_files[$i]);
		}
	}

	$toeic_pdf=new TOEICPDF();
	$toeic_pdf->createAllPages();
	if(isset($_POST["tweet"]) && $_POST["tweet"]==1){
		$toeic_pdf->save("tweet/",$time);//tweetするPDFは完全保存
	}
	$toeic_pdf->save("render/",$time);//どんな場合も一時保存

	$toeic_pdf->init();
	$toeic_pdf->createSecondPage();
	$toeic_pdf->save("render/",$time+1);

	$toeic_pdf->init();
	$toeic_pdf->createThirdPage();
	$toeic_pdf->save("render/",$time+2);
	
	echo $time;
}

function h($word){
	return htmlspecialchars($word);
}

class TOEICPDF{
	public $pdf;
	public function __construct(){
		$this->init();
	}
	public function init(){
		$this->pdf="";
		$this->pdf=new MBFPDF("P","mm","A4");
	}
	public function createFirstPage(){
		$this->pdf->AddPage();
		$this->pdf->SetFont("Arial","B",15);
		$this->pdf->Rect(8,8,194,94);
		$this->pdf->Write(6,"READING TEST");
		$this->pdf->SetFont("Arial","",12);
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->setFont("Arial","",12);
		$this->pdf->Write(6,"In the Reading test, you will read a variety of texts and answer several diffirent types of reading comprehension questions. The entire Reading test will last 75 minutes. There are three parts, and directions are given for each part. You are encouraged to answer as many questions as possible within the time allowed.");
		$this->pdf->SetFont("Arial","",12);
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Write(6,"You must mark your answers on the separate answer sheet. Do not write your answers in your test book.");
		$this->pdf->SetFont("Arial","",12);
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetFont("Arial","B",15);
		$this->pdf->Write(6,"PART 5");
		$this->pdf->SetFont("Arial","",12);
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->setFont("Arial","B",12);
		$this->pdf->Write(6,"Directions: ");
		$this->pdf->setFont("Arial","",12);
		$this->pdf->Write(6,"A word or phrase is missing in each of the sentences below. Four answer choices are given below each sentence. Four answer choices are given below each sentence. Select the best answer to complete the sentence. Then mark the letter (A), (B), (C) or (D) on your answer sheet.");
		$this->pdf->SetFont("Arial","",12);
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetFont("Arial","",12);
		$this->pdf->Ln();
		$this->pdf->Ln();
		for($i=0; $i<4; $i++){
			$this->pdf->setX(8);
			for($j=0; $j<2; $j++){
				$this->pdf->setY(110+$i*43);
				$this->pdf->setX(8+$j*97);
				$this->pdf->setFont("Arial","B",11);
				$this->pdf->Cell(10,6,(101+$i+$j*4).".");
				$this->pdf->setFont("Arial","",11);
				$this->pdf->MultiCell(87,6,h($_POST["questions"][$i+$j*4])."\n(A) ".h($_POST["A"][$i+$j*4])."\n(B) ".h($_POST["B"][$i+$j*4])."\n(C) ".h($_POST["C"][$i+$j*4])."\n(D) ".h($_POST["D"][$i+$j*4]),0);
			}
		}
	}
	public function createSecondPage(){
		$this->pdf->AddPage();
		for($i=0; $i<6; $i++){
			$this->pdf->setX(8);
			for($j=0; $j<2; $j++){
				$this->pdf->setY(10+$i*43);
				$this->pdf->setX(8+$j*97);
				$this->pdf->setFont("Arial","B",11);
				$this->pdf->Cell(10,6,(109+$i+$j*6).".");
				$this->pdf->setFont("Arial","",11);
				$this->pdf->MultiCell(87,6,h($_POST["questions"][8+$i+$j*6])."\n(A) ".h($_POST["A"][8+$i+$j*6])."\n(B) ".h($_POST["B"][8+$i+$j*6])."\n(C) ".h($_POST["C"][8+$i+$j*6])."\n(D) ".h($_POST["D"][8+$i+$j*6]),0);
			}
		}
	}
	public function createThirdPage(){
		$this->pdf->AddPage();
		for($i=0; $i<5; $i++){
			$this->pdf->setX(8);
			for($j=0; $j<2; $j++){
				$this->pdf->setY(10+$i*43);
				$this->pdf->setX(8+$j*97);
				$this->pdf->setFont("Arial","B",11);
				$this->pdf->Cell(10,6,(121+$i+$j*5).".");
				$this->pdf->setFont("Arial","",11);
				$this->pdf->MultiCell(87,6,h($_POST["questions"][20+$i+$j*5])."\n(A) ".h($_POST["A"][20+$i+$j*5])."\n(B) ".h($_POST["B"][20+$i+$j*5])."\n(C) ".h($_POST["C"][20+$i+$j*5])."\n(D) ".h($_POST["D"][20+$i+$j*5]),0);
			}
		}
	}
	public function createAllPages(){
		$this->createFirstPage();
		$this->createSecondPage();
		$this->createThirdPage();
	}
	public function save($dir="tweet/",$name){
		$this->pdf->Output("../pdf/".$dir.$name.".pdf","F");
	}
}
?>