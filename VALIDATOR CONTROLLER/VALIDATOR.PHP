<?php
class ValidationsController extends Controller
{
    function containsNumbers($str){
        return (preg_match('/\\d/', $str) > 0);
    }

    function checkCPF($cpf){

        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
    
    
    function validaSenha($senha, $confirmacao_de_senha = "")
    {
        $isValido = true;
    
        //verifica se a senha está vazia
        if (empty($senha) or empty($confirmacao_de_senha)) {
            $isValido = false;
        }
        //verifica se a senha e a confirmação sao iguais
        else if ($senha != $confirmacao_de_senha) {
            $isValido = false;
        }
        //verifica o tamanho da senha
        else if (strlen($senha) < 8) {
            $isValido = false;
        }
    
        return $isValido;
    }
    
    function formataCpf($cpf){    
        $bloco_1 = substr($cpf, 0, 3);
        $bloco_2 = substr($cpf, 3, 3);
        $bloco_3 = substr($cpf, 6, 3);
        $dig_verificador = substr($cpf, -2);
        $cpf_formatado = $bloco_1 . "." . $bloco_2 . "." . $bloco_3 . "-" . $dig_verificador;
        return $cpf_formatado;
    }

    function checkName($name,$count){
        return (strlen($name) > 2 && $this->containsNumbers($name) == false && count(explode(' ',$name)) >= $count);
    }

    function formatPhone($phone, $type, $dDDD){
        switch (strlen($phone)) {
            case ((9 & $type != 1) || (10 & $type != 0)):
                $phone = $dDDD.$phone;
                break;
            case ((11 & $type != 1) || (8 & $type != 0)):
                break;
            default:
                return false;
                break;
        }
        return $phone;
    }

    function checkLength($str,$justNumbers,$firstV, $lastV = 0){
        if($justNumbers)
            $str = preg_replace('/[^0-9]/is', '', $str);
        if($lastV != 0)
            return (strlen($str) >= $firstV && strlen($str) <= $lastV);
        else
            return (strlen($str) == $firstV);
    }

    function checkPhone($phone){
        return $this->checkLength($phone,true,8,11);
    }

    function checkCEP($cep){
        return $this->checkLength($cep,true,8);
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function checkAddress($zipcode,$street,$number,$neighborhood,$city,$state,$country){
        return(
        $this->checkCEP($zipcode) &&
        $this->checkName($street,1) &&
        $this->containsNumbers($number) &&
        $this->checkName($neighborhood,1) &&
        $this->checkName($city,1) &&
        $this->checkName($state,1) &&
        $this->checkName($country,1)
        );
    }

    function replaceAllObj($fields, $str, $rep, $expetion){
        $newFields=$fields;
        foreach($fields as $i => $v){
            if($i != $expetion)
                $newFields[$i] = str_replace($str, $rep, $v);
        }
        return $newFields;
    }
    
    function checkFields($fields, $keys){
        if(count($fields) < count($keys))
            return false;

        foreach($keys as $indice => $key){
            if(!isset($fields[$key])){
                var_dump(isset($fields[$key]),$key);
                return false;
            }
        }

        return true;
    }    

    public function captchaCheck($captcha){
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $data = [
                'secret' => config('services.recaptcha.secret'),
                'response' => $captcha,
                'remoteip' => $remoteip
              ];
        $options = [
                'http' => [
                  'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                  'method' => 'POST',
                  'content' => http_build_query($data)
                ]
            ];
        $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                $resultJson = json_decode($result);
        if ($resultJson->success != true) {
                return false;
                // return back()->withErrors(['captcha' => 'ReCaptcha Error']);
                }
        if ($resultJson->score >= 0.6) {
                return true;
                //Validation was successful, add your form submission logic here
                // return back()->with('message', 'Thanks for your message!');
        } else {
                return false;
                // return back()->withErrors(['captcha' => 'ReCaptcha Error']);
        }
      }
}
?>