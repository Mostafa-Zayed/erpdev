<?php

return [
    'document_size_limit' => '5000000', //in Bytes,
    'image_size_limit' => '5000000', //in Bytes
    'document_upload_mimes_types' => [
        'application/pdf' => '.pdf',
        'text/csv' => '.csv',
        'application/zip' => '.zip',
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'image/jpeg' => '.jpeg',
        'image/jpg' => '.jpg',
        'image/png' => '.png'

    ],
];
