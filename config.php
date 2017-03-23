<?php
/** Конфигурационный файл сайта "ЭКО-ЭФФЕКТ". */

// =====================================================================================================================
// Режим отладки.
// =====================================================================================================================
define('IS_DEBUG_MODE', true);

// =====================================================================================================================
// Данные об адресе сайта.
// =====================================================================================================================
/** Абсолютный адрес сайта. */
define('SITE_URL', 'http://eco-effect.loc/');

// =====================================================================================================================
// Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера.
// =====================================================================================================================
/** Хост MySQL базы данных. */
define('DB_HOST', 'localhost');

/** Имя MySQL базы данных. */
define('DB_NAME', 'eco_effect');

/** Имя пользователя, у которого есть права работать в MySQL базе данных. */
define('DB_USERNAME', 'mysql');

/** Пароль для доступа к MySQL базе данных. */
define('DB_PASSWORD', 'mysql');

// =====================================================================================================================
// Константы для загружаемых фотографий.
// =====================================================================================================================
/** Максимально допустимый размер загружаемой фотографии. */
define('MAX_IMG_SIZE', 8388608);

/** Максимально допустимая сумма ширины и высоты загружаемой фотографии. */
define('MAX_WIDTH_HEIGHT_SUM', 14000);

/**
 * Ширина миниатюры. Если ширина самой фотографии равна или меньше указанной ширины миниатюры, то она создаваться
 * не будет!
 */
define('THUMBNAIL_WIDTH', 490);

// =====================================================================================================================
// Данные конкурса.
// =====================================================================================================================
/** Массив VK идентификаторов администраторов конкурса. */
define('ADMINS', [
    77335203,
    21047821
]);

/** Активен ли конкурс в данный момент. */
define('COMPETITION_ACTIVE', true);

/** Час, в который будут определяться победители конкурса. */
define('GET_WINNER_HOUR', 20);

/** Дата начала проведения конкурса. */
define('COMPETITION_START', '20.02.2017');

define('WINNERS', [
    1 => [
        'winner_0' => ['photo' => 26, 'uid' => 222577551],
        'winner_1' => ['photo' => 27, 'uid' => 77335203]
    ],
    2 => [
        'winner_0' => ['photo' => 27, 'uid' => 77335203],
        'winner_1' => ['photo' => 28, 'uid' => 77335203]
    ],
    3 =>    null,
    4 =>    null,
    5 =>    null,
    6 =>    null,
    7 =>    null,
    8 =>    null,
    9 =>    null,
    10 =>   null
]);

/** Спонсоры. */
define('SPONSORS', [
    [
        'title' =>  'Фонд поддержки молодежных инициатив &laquo;ЭРА&raquo;',
        'url' =>    'http://enter505.ru/companies/m0736104',
        'img' =>    'era.png'
    ],
    [
        'title' =>  'Центр экономии ресурсов',
        'url' =>    'http://centrecon.ru/',
        'img' =>    'economy-center.png'
    ],
    [
        'title' =>  'Зеленое движение России &laquo;ЭКА&raquo;',
        'url' =>    'http://ecamir.ru/',
        'img' =>    'ecamir.png'
    ],
    [
        'title' => 'Благотворительный фонд &laquo;Coca-Cola Foundation&raquo;',
        'url' =>    'http://www.coca-colacompany.com/our-company/the-coca-cola-foundation',
        'img' =>    'coca-cola.png'
    ]
]);

/** Включены ли комментарии. */
define('ALLOW_COMMENTS', true);

// =====================================================================================================================
// Данные VK API.
// =====================================================================================================================
/** ID приложения. */
define('VK_CLIENT_ID', 5744801);

/** Защищенный ключ. */
define('VK_SECRET', '92odiCNlyw3uMpHSYh45');

/** Сервисный ключ доступа. */
define('VK_SERVICE', '66e753f666e753f666b81a6e7966b0fb57666e766e753f63e4209265d07ed97fbd52477');

/** Случайное константное значение, используемое для обнуления всех счетчиков лайков и виджетов комментариев. */
define('VK_WIDGETS_SALT', '4944b03f933b66c3519cc7ee201a814971b51512e72b962e1bfa58bb0c30f599');

// =====================================================================================================================
// Конец конфига.
// =====================================================================================================================
if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

if (!defined('MODULES'))
    define('MODULES', ABSPATH . 'modules/');