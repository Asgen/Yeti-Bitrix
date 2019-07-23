<?

$GLOBALS['stuff_iblock'] = 8;

AddEventHandler("main", "OnBeforeUserRegister", Array("MyClass", "OnBeforeUserRegisterHandler"));
class MyClass
{
   function OnBeforeUserRegisterHandler(&$arFields)
    {
          $arFields["LOGIN"] = $arFields["EMAIL"];
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}


function custom_mail(){
        // подключаем библиотеку

 $transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

// Создадим главный объект библиотеки SwiftMailer, ответственный за отправку сообщений. Передадим туда созданный объект с SMTP-сервером.
$mailer = new Swift_Mailer($transport);
   

   
   // подключаем плагин логов
   $logger = new Swift_Plugins_Loggers_ArrayLogger();
   $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
   
   // отправляем

   $message = new Swift_Message();
$message->setSubject("Уведомление от сервиса «Дела в порядке»");
$message->setFrom(['keks@phpdemo.ru' => 'Doingsdone']);
   $message->setTo('genuus1@gmail.com');
 $result = $mailer->send($message);
    if(!$result){
        AddMessage2Log(print_r(array("BAD_MAIL" => $failures, "LOG" => $logger->dump()),true));
    }
    return $result;
}

?>