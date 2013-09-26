<?php

require '/php/PhoneInfo.php';

$phone = !empty($_GET['phone']) ? $_GET['phone'] : '';

$htmlClass = $phone ? 'phone-number' : 'index';

$phoneInfo = new PhoneInfo();
$phoneInfo = $phoneInfo->execute($phone);

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="ru" class="no-js lt-ie9 lt-ie8 lt-ie7 <?php echo $htmlClass; ?>"> <![endif]-->
<!--[if IE 7]>         <html lang="ru" class="no-js lt-ie9 lt-ie8 <?php echo $htmlClass; ?>"> <![endif]-->
<!--[if IE 8]>         <html lang="ru" class="no-js lt-ie9 <?php echo $htmlClass; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="ru" class="no-js <?php echo $htmlClass; ?>"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <?php
        if (!$phoneInfo):
            ?><title>Телефонный справочник Киева 2013, база 09 Киев и Киевская область, телефонная база г. Киев</title><!-- с картой -->
        <meta name="description" content="☎ Телефонный справочник г. Киев, база 09 Киева и области, поиск людей по телефону, фамилии или адресу">
        <?php
        else:
            ?><title>Телефонный справочник Киева: (044) <?php echo $phoneInfo['phoneNumber']; ?>, <?php echo rtrim($phoneInfo['secondName'] . ' ' . $phoneInfo['firstName'] . ' ' . $phoneInfo['middleName']); ?>, <?php echo $phoneInfo['street'] . ($phoneInfo['house'] ? ', дом ' . $phoneInfo['house'] : '') . ($phoneInfo['room'] ? ', кв. ' . $phoneInfo['room'] : ''); ?></title>
        <meta name="description" content="☎ Результаты поиска. Телефон: (044) <?php echo $phoneInfo['phoneNumber']; ?>. ФИО: <?php echo str_replace('"', "'", rtrim($phoneInfo['secondName'] . ' ' . $phoneInfo['firstName'] . ' ' . $phoneInfo['middleName'])); ?>. Адрес: <?php echo $phoneInfo['street'] . ($phoneInfo['house'] ? ', дом ' . $phoneInfo['house'] : '') . ($phoneInfo['room'] ? ', кв. ' . $phoneInfo['room'] : ''); ?>">
        <?php
        endif;
        ?><meta name="keywords" content="Телефонный справочник, база 09, Киев, Киевская область, телефонная база, база телефонов, справочник номеров, телефонная книга, поиск людей по номеру телефона">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/main.css">

        <link rel="shortcut icon" href="/favicon.ico">

        <!--link rel="stylesheet/less" type="text/css" href="styles.less" />
        <script src="/js/less.js" type="text/javascript"></script--s>

        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="js/libs/html5shiv.js"><\/script>')</script>
        <![endif]-->

        <!-- Made in Ukraine -->
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <div class="container">

            <h1>База 09 г. Киева</h1>

            <?php if ($phoneInfo): ?>
            <address id="phone-info">
                <dl class="dl-horizontal">
                    <dt>Телефон:</dt>
                    <dd>(044) <?php echo $phoneInfo['phoneNumber']; ?></dd>
                    <dt>ФИО:</dt>
                    <dd><?php echo $phoneInfo['secondName'] . ' ' . $phoneInfo['firstName'] . ' ' . $phoneInfo['middleName']; ?></dd>
                    <dt>Адрес:</dt>
                    <dd><?php echo $phoneInfo['street'] . ($phoneInfo['house'] ? ', ' . $phoneInfo['house'] : '') . ($phoneInfo['room'] ? ', кв. ' . $phoneInfo['room'] : ''); ?></dd>
                </dl>
            </address>
            <?php endif; ?>

            <form id="form">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="phone-label" for="phone-number">Телефон:</label>

                            <div class="input-group">
                                <span class="input-group-addon phone-groups-addon">044</span>
                                <input type="tel" class="form-control input-large" name="phone-number" id="phone-number" autofocus>
                            </div>

                            <span class="field-info">Все 7 цифр или начало номера</span>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="second-name">Фамилия:</label>
                            <input type="text" class="form-control" name="second-name" id="second-name">
                            <span class="field-info">Вы можете ввести только начало фамилии</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="first-name">Имя:</label>
                            <input type="text" class="form-control" name="first-name" id="first-name">
                            <span class="field-info">Первая буква имени</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="middle-name">Отчество:</label>
                            <input type="text" class="form-control" name="middle-name" id="middle-name">
                            <span class="field-info">Первая буква отчества</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="street">Улица:</label>
                            <input type="text" class="form-control" name="street" id="street">
                            <span class="field-info">Вы можете ввести только начало названия улицы</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="house">Дом:</label>
                            <input type="text" class="form-control" name="house" id="house">
                            <span class="field-info">Номер и буква дома</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="room">Квартира:</label>
                            <input type="text" class="form-control" name="room" id="room">
                            <span class="field-info">Номер и буква квартиры</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" id="searchButton" class="btn btn-primary">Поиск</button>
                        <img src="img/loader.gif" width="32" height="32" alt="" class="loader">
                    </div>
                </div>

                <table class="table table-striped" id="results-table">
                    <thead>
                        <tr>
                            <th>Телефон</th>
                            <th>Фамилия</th>
                            <th>Имя</th>
                            <th>Отчество</th>
                            <th>Улица</th>
                            <th>Дом</th>
                            <th>Квартира</th>
                        </tr>
                    <thead>
                    <tbody id="found-items"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                <a href="#" id="up">Наверх страницы</a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div id="total-found"></div>

                <div id="no-results">Ничего не найдено</div>

                <div id="map"></div>

            </form>

            <article style="color: #999; font-size: 10px;">Телефонный справочник 2013 г. Киева и Киевской области. Самая свежая база 09 городских телефонных номеров Киева. Телефонная база стационархых телефонов Киева. Удобный поиск людей по телефону, фамилии или адресу.</article>

        </div>

        <script src="/js/libs/require.js" data-main="/js/app.js"></script>

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-3766727-4', 'baza09.com.ua');
            ga('send', 'pageview');
        </script>
    </body>
</html>
