<?php
/** Тест работы с Amazon */

require_once('load.php');

require_once(ABSPATH . 'amazon/aws.phar');

use Aws\S3\S3Client;

// Instantiate an Amazon S3 client.
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1',
    'credentials' => [
        'key' =>    AWS_ACCESS_KEY_ID,
        'secret' => AWS_SECRET_ACCESS_KEY
    ]
]);

$s3->putObject([
    'Bucket' => 'eco-effect',
    'Key'    => 'image.png',
    'Body'   => 'some-body',
]);