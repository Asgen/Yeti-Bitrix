<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER;
if (!$USER->IsAuthorized()) {
    header("Location: /local/auth/index.php") ;
};

//Подключаем модуль инфоблоков
CModule::IncludeModule('iblock');
$IBLOCK_ID = $GLOBALS['stuff_iblock']; //ИД инфоблока с которым работаем

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Валидация формы добавления лота

    $lot = $_POST;
    $lot_img = $_FILES['image_detail'];
    $required = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $errors = [];

    foreach ($lot as $key => $value) {
        $lot[$key] = trim($value);
    }

    // Проверка заполненности полей
    foreach ($required as $key) {
        if (empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
    
    if ($lot_img['error'] !== 0) {
        $errors['image'] = 'Загрузите изображение лота, в формате jpeg или png';
    } elseif (mime_content_type($lot_img['tmp_name']) !== "image/png" && mime_content_type($lot_img['tmp_name']) !== "image/jpeg") {
        $errors['image'] = 'Поддерживаемы форматы изображения - jpeg и png';
    }


    // Проверка цены и ставки
    if (!empty($errors['lot-rate'])) {
        $errors['lot-rate'] = 'Введите начальную цену';
    } elseif (empty($errors['lot-rate']) && !is_int($lot['lot-rate']) && $lot['lot-rate'] < 1) {
        $errors['lot-rate'] = 'Введите целое число больше нуля';
    }
    if (!empty($errors['lot-step'])) {
        $errors['lot-step'] = 'Введите шаг ставки';
    } elseif (empty($errors['lot-step']) && !is_int($lot['lot-step']) && $lot['lot-step'] < 1) {
        $errors['lot-step'] = 'Введите целое число больше нуля';
    }

    // Проверка даты
    $tomorrow = strtotime('tomorrow');
    $lot_date = $lot['lot-date'];
    $lot_date = strtotime($lot_date);

    if (!$lot_date || ($lot_date - $tomorrow < 0)) {
        $errors['date'] = "Указанная дата должна быть больше текущей даты, хотя бы на один день";
    } else {
        $lot_date = ConvertTimeStamp($lot_date);
    }


    // Если ошибок нет
    if (!count($errors)) {
        $el = new CIBlockElement;
        $user_id = $GLOBALS['USER']->GetID();

        // Свойства
        $start_price = $lot['lot-rate'];
        $price_step = $lot['lot-step'];

        $PROP = array();
        $PROP["PRICE_START"] = $start_price;
        $PROP["PRICE_GAP"] = $price_step;
        $PROP["PRICE_CURRENT"] = $start_price;
        $PROP["FINISH_DATE"] = $lot_date;
        $PROP["WINNER"] = '0';


        // Основные поля элемента
        $fields = array(
            "DATE_CREATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time()), //Передаем дата создания
            "CREATED_BY" => $user_id,    // Передаем ID пользователя кто добавляет
            'IBLOCK_SECTION_ID' => (int)$lot['category'], // ID раздела
            "IBLOCK_ID" => $IBLOCK_ID, // ID информационного блока
            "CODE" => "lot" . $user_id . uniqid(), // Символьный код
            "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
            "NAME" => strip_tags($lot['lot-name']),
            "ACTIVE" => "Y",
            "PREVIEW_TEXT" => strip_tags($lot['description']), // Анонс
            "PREVIEW_PICTURE" => $_FILES['image_detail'], // Изображение для анонса
            "DETAIL_TEXT" => strip_tags($lot['message']),
            "DETAIL_PICTURE" => $_FILES['image_detail']
        );
        
        //Результат в конце отработки
        if ($ID = $el->Add($fields)) {
            echo "Сохранено";

            $res = CIBlockSection::GetByID((int)$lot['category']);
            if ($ar_res = $res->GetNext()) {
                header("Location: " . SITE_TEMPLATE_PATH . $ar_res['SECTION_PAGE_URL'] . "?ELEMENT_ID=" . $ID);
            }
        } else {
            echo 'Произошел как-то косяк Попробуйте еще разок';
        }
    }
}


// Поключение лэйаута с включением в него шаблона
$layout_content = include_template('add_lot.php', [
    'errors' => $errors,
    'lot' => $lot

]);

print($layout_content); ?>

<script>
	var previewBlock = document.querySelector('.preview');
	var removeButton = previewBlock.querySelector('.preview__remove');	
	var previewImg = previewBlock.querySelector('.preview__img img');
	var imageInput = document.querySelector('input[name="image_detail"]');

	imageInput.onchange = function(event) {
	   var file = imageInput.files[0];
	   if (file) {
	      var fileName = file.name.toLowerCase();     
          var reader = new FileReader();

          reader.addEventListener('load', function () {
            previewImg.src = reader.result;
            previewBlock.style.display = 'block';
            previewBlock.style.position = 'relative';
          });

          reader.readAsDataURL(file);
	      
	    }
	}
	removeButton.onclick = function(evt) {
		previewImg.src = '';
        previewBlock.style.display = 'none';
        previewBlock.style.position = 'absolute';
	}	
</script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>