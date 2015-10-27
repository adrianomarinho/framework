<?php
namespace Controllers;

use Core\Controller;
use Core\View;
use Core\Database;

use Helpers\Data;
use Helpers\Encrypt;
use Helpers\Validations;
use Helpers\Url;
use Helpers\MyCurl;

use Helpers\Template;

use Helpers\DateHour;

class Tests
{
	
    public function index()
    {
    	
    	$data['subtitle'] = 'Welcome';
    	$data['sitetitle'] = SITETITLE;
    	$data['dir'] = DIR;

    	$temp = new Template;
    	$temp->renderPrint('site/html/header.html', $data);
    	$temp->renderPrint('site/html/home.html', $data);
    	$temp->renderPrint('site/html/footer.html', $data);
    	
    }
	
    public function testUrl($param)
    {

    	$a = [];
        $a['param'] = $param;
        $a['get'] = $_GET;

        Data::pr($a);

    }

    public function info()
    {
    	phpinfo();
    }
 
    
	public function mail(){
		
		$param['type'] = 'smtp';
		
		$mail = new \Helpers\Mail($param);
		
		$assunto = "BF1 - Teste email";
		$mensagem = "Mensagem teste Babita Framework 1 (ÁÂÀÄÇ$!@&%)";
		
		$from = array('mail' => 'fabiioassuncao@gmail.com', 'name' => 'Babita Framework 1');
		
		$replyTo = array('mail' => 'fabio@fabioassuncao.com.br', 'name' => 'Contato BF1');
		
		$destino = array(
				array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção'),
				array('mail' => 'fabio.as@live.com', 'name' => 'Adriano Marinho'),
			);

		$result = $mail->quick($assunto, $mensagem, $from, $destino, $replyTo);
		Data::vd($result);
		
	}
	
	public function mailTemplate(){
		
		$mail = new \Helpers\Mail();
		
		$data = array(
				'title' => 'Babita Framework 1',
				'subtitle' => 'Teste com template',
				'name' => 'Fábio Assunção',
				'link' => DIR,
				'link_name' => 'Botão'
				);
		
		$mail->subject("BF1 - email com anexo e template :)");
		$mail->template('mail/test.html', $data);
		$mail->destination(array(
				//"aangelomarinho@gmail.com, Adriano Marinho",
				"fabio23gt@gmail.com, Fábio Assunção"
		));

		$mail->attachment('storage/uploads/teste_anexo.png, teste_anexo.png');
		
		$mail->from(array('mail' => 'fabio@fabioassuncao.com.br', 'name' => 'Babita Framework 1'), false);
		$mail->replyTo(array('mail' => 'contato@bf1.com', 'name' => 'BF1 Contato novo'));

 		$result = $mail->go();
 		Data::vd($result);
	
	}
	
	public function mailList(){
	
		$mail = new \Helpers\Mail();
	
		$data = array(
				'title' => 'Babita Framework 1',
				'subtitle' => 'Test mailing list with template',
				'name' => 'User test',
				'link' => '#',
				'link_name' => 'Botão'
		);
	
		$mail->subject("BF1 - mailing list with template");
		$mail->template('mail/test.html', $data);
		$mail->from(array('mail' => 'fabio@fabioassuncao.com.br', 'name' => 'Babita Framework 1'));
		
		$result = $mail->mailingList(array(
				"aangelomarinho@gmail.com, Adriano Marinho",
				"fabio23gt@gmail.com, Fábio Assunção",
				"fabio.as@live.com, Fabio AS",
				"fabio@ma.ip.tv, Fabio IPTV",
				"fabiioassuncao@gmail.com, Fabio IPTV"
		));

		Data::json($result);
	
	}
	
	public function mailListLoop(){
	
		$mail = new \Helpers\Mail();
		$mail->subject("BF1 - mailing list with template");
		$mail->from(array('mail' => 'fabio@fabioassuncao.com.br', 'name' => 'Babita Framework 1'));

		$list = array(
			array('mail' => 'aangelomarinho@gmail.com', 'name' => 'Adriano Marinho'),
			array('mail' => 'fabio23gt@gmail.com', 		'name' => 'Fábio Assunção'),
			array('mail' => 'fabio.as@live.com', 		'name' => 'Fabio AS'),
			array('mail' => 'fabio@ma.ip.tv', 			'name' => 'Fabio IPTV'),
			array('mail' => 'fabiioassuncao@gmail.com', 'name' => 'Fabio 2 GMAIL')
		);
		
		foreach($list as $l){
			
			$mail->template('mail/test.html', array(
					'title' => 'Babita Framework 1',
					'subtitle' => 'Test mailing list with template',
					'name' => $l['name'],
					'link' => '#',
					'link_name' => $l['mail']
			));
			
			$mail->destination( array('mail' => $l['mail'], 'name' => $l['name']), true );
			
		}
		
		Data::json($mail->log());
	
	}
}