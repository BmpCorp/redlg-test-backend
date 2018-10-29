<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Intervention\Image\Facades\Image as ImageManager;
use Intervention\Image\Image as Image;

class FileUploadController extends Controller
{
    /**
     * Определяет тип загруженного файла и соответствующий метод обработки.
     * @param  Request $obRequest Данные поступившего запроса.
     * @return void
     */
    public function process(Request $obRequest)
    {
        $obFile = $obRequest->file("file");

        if (!$obFile) {
            echo "Нет файла";
            die();
        }

        // У файла с JSON-данными необязательно должно быть расширение .json,
        // а именно по нему браузер и, соответственно, getClientMimeType()
        // определяет, что это JSON-данные. Поэтому нельзя полагаться на этот
        // метод. Попробуем преобразовать содержимое файла в JSON.
        $obJSONData = json_decode($obFile->get());

        if ($obJSONData) {
            $this->processJSON($obJSONData);
        } else {
            if ($obFile->getMimeType() === "image/jpeg") {
                $obImage = ImageManager::make($obFile);
                $this->processJPEG($obImage);
            } elseif ($obFile->getMimeType() === "image/png") {
                $obImage = ImageManager::make($obFile);
                $this->processPNG($obImage);
            } else {
                $this->processOther();
            }
        }
    }

    /**
     * Обработать поступившие JSON-данные.
     * @param  array/object $obJSONData JSON-данные.
     * @return void
     */
    private function processJSON($obJSONData)
    {
        dd($obJSONData);
    }

    /**
     * Обработать изображение в формате JPEG.
     * @param  Image  $obImage Изображение Intervention/Image.
     * @return void
     */
    private function processJPEG(Image $obImage)
    {
        dd($obImage);
    }

    /**
     * Обработать изображение в формате PNG.
     * @param  Image  $obImage Изображение Intervention/Image.
     * @return void
     */
    private function processPNG(Image $obImage)
    {
        dd($obImage);
    }

    /**
     * Обработать файл иного типа.
     * @return void
     */
    private function processOther()
    {
        echo "Не знаю, что это";
    }
}
