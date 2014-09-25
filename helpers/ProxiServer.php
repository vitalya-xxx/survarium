<?php

class ProxiServer {
    private $body;
    
    public function __construct($host){
        // открываем сокет на стандартном для HTTP порту 80
        $hSocket = fsockopen($host, 80, $errno, $errstr, 30);
        if ($hSocket)
        {
            // первой строкой идет строка запроса
            $request = "POST / HTTP/1.1\r\n";
            // указываем доменное имя
            $request .= "Host: $host\r\n";
             // пусть мы будем для сервера браузером Mozilla Firefox
            $request .= "User-Agent: Mozilla/5.0 (X11; U; Linux i686; ru; rv:1.9.0.8) Gecko/2009032600 SUSE/3.0.8-1.1.1 Firefox/3.0.\r\n";
            // указываем форматы, в которых будем ожидать ответ от сервера
            $request .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
            // языки, на которых ожидаем ответ
            $request .= "Accept-Language: ru,en-us;q=0.7,en;q=0.3\r\n";
            // указываем какое кодирование к содержимому допустимо в ответе. 
            //Только в этом случае нужно будет раскодировать содержимое.
            //$request .= "Accept-Encoding: gzip,deflate\r\n"; 
            // выставляем предпочтения для кодировок ответа
            $request .= "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7\r\n";
            // будем удерживать постоянное соединение в течение 300 секунд
            $request .= "Keep-Alive: 300\r\n";
            // открываем постоянное соединение
            // здесь мы завершаем передачу HTTP-заголовков, 
            // поэтому в конце обязательно указываем 2 перевода строки
            $request .= "Connection: keep-alive\r\n\r\n";
            // посылаем HTTP-заголовки через открытый сокет
            fwrite($hSocket, $request);
            // флаг, указывающий на то, что считывается тело запроса
            $bIsData = false;
            while (!feof($hSocket)) // считываем ответ от сервера
            {
                $str = fgets($hSocket, 128); // построчно
                if($bIsData)
                {
                    $this->body .= $str; // тело запроса записываем в переменную $this->body
                }
                // встретилась строка, содержащая только перевод строки, это означает, 
                //что дальше пойдет тело запроса
                elseif($str == "\r\n")
                {
                    $bIsData = true;
                }
            }
            fclose($hSocket); // закрываем сокет
        }
    }
    
    public function sendRespons(){
        // указываем на то, чтобы web-сервер отправил браузеру заголовки 
        // Last-Modified, Connection и Keep-alive
        header("Last-Modified: Tue, 21 Jul 2009 17:09:54 GMT", true);
        header("Connection: Keep-Alive", true); //
        header("Keep-Alive: timeout=30, max=100", true);
        // передаем тело запроса
        echo $this->body;
    }      
}
