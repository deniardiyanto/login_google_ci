<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct(){
		parent::__construct();
		// load google library 
		$this->load->library('google'); 
		// Load model user
		$this->load->model('pengguna'); 
	}
	public function index(){
	// Redirect ke halaman profile jika pengguna sudah login
		if($this->session->userdata('telahLogin') == true){
		redirect('Welcome/profil/');
		}
		if(isset($_GET['code'])){
			// Otentikasi pengguna dengan google
			$client = $this->google;
			$client->authenticate($_GET['code']);
			# ambil profilenya
			$gp = new Google_Service_Plus($client);
			$profil = $gp->people->get("me"); 
			// data untuk di input ke database
			$dataPengguna['oauth_provider'] = 'google';
			$dataPengguna['oauth_uid'] = $profil['id'];
			$dataPengguna['nama_depan'] = $profil['name']['familyName'];
			$dataPengguna['nama_belakang'] = $profil['name']['givenName'];
			$dataPengguna['email'] = $profil['emails']['0']['value'];
			$dataPengguna['jenis_kelamin'] = !empty($profil['gender'])?$profil['gender']:'';
			$dataPengguna['photo'] = !empty($profil['image']['url'])?$profil['image']['url']:'';
			// Insert atau update data pengguna di database
			$userID = $this->pengguna->checkUser($dataPengguna);
			// simpan session
			$this->session->set_userdata('telahLogin', true);
			$this->session->set_userdata('dataPengguna', $dataPengguna);
			// Redirect to profile page
			redirect('Welcome/profil/');
		} 
		// Google authentication url
		$data['loginURL'] = $this->google->createAuthUrl();
		// Load google login view
		$this->load->view('login',$data);
	}

	public function profil(){
		// Redirect to login page if the user not logged in
		if(!$this->session->userdata('telahLogin')){
			redirect('/Welcome/');
		}
		// Get user info from session
		$data['dataPengguna'] = $this->session->userdata('dataPengguna');
		// Load user profile view
		$this->load->view('pengguna/profil',$data);
	}
	public function keluar(){
		// Reset OAuth access token
		$this->google->revokeToken();
		// Remove token and user data from the session
		$this->session->unset_userdata('telahLogin');
		$this->session->unset_userdata('dataPengguna');
		// Destroy entire session data
		$this->session->sess_destroy();
		// Redirect to login page
		redirect('welcome');
	} 
}