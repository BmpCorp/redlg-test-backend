<?php

return [

    /**
     * Максимальное качество JPG-изображений при сохранении.
     */

    'max_jpg_quality' => 70,

    /**
     *  Максимальный размер приложенного файла при отправлении заявки.
     */

    'max_attachment_size' => 3 * 1024 * 1024,

    /**
     * Допустимые типы файла, прилагаемого к заявке во втором задании.
     */

    'allowed_doc_types' => [
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
    ],

];
