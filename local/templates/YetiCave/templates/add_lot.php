<form class="form form--add-lot container" action="<?= SITE_TEMPLATE_PATH ?>/add_lot.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
  <h2>Добавление лота</h2>
  <div class="form__container-two">
    <div class="form__item <?= !empty($errors['lot-name']) ? ' form__item--invalid' : '' ?>"> <!-- form__item--invalid -->
      <label for="lot-name">Наименование</label>
      <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= $lot['lot-name'] ?>">
      <?php if (!empty($errors['lot-name'])) : ?>
      <span class="form__error">Введите наименование лота</span>
      <?php endif; ?>
    </div>
    <div class="form__item <?= !empty($errors['category']) ? ' form__item--invalid' : '' ?>">
      <label for="category">Категория</label>
      <select id="category" name="category" >
        <option value="">Выберите категорию</option>
        <?php
        $arIBTYPE = CIBlockType::GetByIDLang("catalog", LANGUAGE_ID);
        if ($arIBTYPE!==false) {
            $arFilter = array('IBLOCK_ID'=>$IBLOCK_ID, 'GLOBAL_ACTIVE'=>'Y');
            $db_list = CIBlockSection::GetList(array("SORT"=>"ASC"));
            $db_list->NavStart(20);
            echo $db_list->NavPrint($arIBTYPE["SECTION_NAME"]);
            while ($ar_result = $db_list->GetNext()) : ?>
           
          <option value="<?= $ar_result['ID']; ?>" <?= !empty($lot['category']) && $lot['category'] === $ar_result['ID'] ? 'selected' : '' ?>><?= $ar_result['NAME']; ?></option>

        <?php endwhile;
        } ?>

      </select>
      <?php if (!empty($errors['category'])) : ?>
        <span class="form__error">Выберите категорию</span>
      <? endif; ?>
    </div>
  </div>

  <div class="form__item <?= !empty($errors['message']) ? ' form__item--invalid' : '' ?> form__item--wide">
    <label for="message">Описание</label>
    <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $lot['message'] ?></textarea>
    <?php if (!empty($errors['message'])) : ?>
      <span class="form__error">Напишите описание лота</span>
    <? endif; ?>
  </div>
  <div class="form__item form__item--file <?= !empty($_FILES['image_detail']['tmp_name']) ? ' form__item--uploaded' : ''  ?> "> <!-- form__item--uploaded -->
    <label>Изображение</label>
    <div class="preview">
      <button class="preview__remove" type="button">x</button>
      <div class="preview__img">
        <img src="<?= $_FILES['image_detail']['tmp_name'] ?>" width="113" height="113" alt="Изображение лота">
      </div>
    </div>
    <div class="form__input-file">
      <input name="image_detail" class="visually-hidden" type="file" id="photo2" value="<?= $_FILES['image_detail'] ?>">
      <label for="photo2">
      <span>+ Добавить</span>
      </label>
    </div>
          <?php if (!empty($errors['image'])) : ?>
        <span class="form__error"><?= $errors['image'] ?></span>
      <? endif; ?>
  </div>
  <div class="form__container-three">
    <div class="form__item form__item--small <?= !empty($errors['lot-rate']) ? ' form__item--invalid' : '' ?>">
      <label for="lot-rate">Начальная цена</label>
      <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?= $lot['lot-rate'] ?>">
      <?php if (!empty($errors['lot-rate'])) : ?>
        <span class="form__error"><?= $errors['lot-rate'] ?></span>
      <? endif; ?>
    </div>
    <div class="form__item form__item--small <?= !empty($errors['lot-step']) ? ' form__item--invalid' : '' ?>">
      <label for="lot-step">Шаг ставки</label>
      <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?= $lot['lot-step'] ?>">
      <?php if (!empty($errors['lot-step'])) : ?>
        <span class="form__error"><?= $errors['lot-step'] ?></span>
      <? endif; ?>
    </div>
    <div class="form__item <?= !empty($errors['lot-date']) ? ' form__item--invalid' : '' ?>">
      <label for="lot-date">Дата окончания торгов</label>
      <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?= $lot['lot-date'] ?>">
      <?php if (!empty($errors['lot-date'])) : ?>
        <span class="form__error">Введите дату завершения торгов</span>
      <?php elseif (!empty($errors['date'])) : ?>
        <span class="form__error"><?= $errors['date'] ?></span>
      <? endif; ?>
    </div>
  </div>
  <?php if (!empty($errors)) : ?>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
  <? elseif(!empty($errors['invalid-number'])) : ?>
    <p class="form__error form__error--bottom"><?= $errors['invalid-number'] ?></p>
  <? endif; ?>
  <button type="submit" class="button">Добавить лот</button>
</form>