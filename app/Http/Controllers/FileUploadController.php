<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Intervention\Image\Facades\Image;

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

        if (!$obFile || !$obFile->isValid()) {
            return $this->uploadError();
        }

        if (filesize($obFile) > config("tasks.max_file_size")) {
            return $this->fileSizeError();
        }

        // У файла с JSON-данными необязательно должно быть расширение .json,
        // а именно по нему браузер и, соответственно, getClientMimeType()
        // определяет, что это JSON-данные. Поэтому нельзя полагаться на этот
        // метод. Попробуем преобразовать содержимое файла в JSON.
        $obJSONData = json_decode($obFile->get());

        if ($obJSONData) {
            return $this->processJSON($obJSONData);
        }

        // Если не получилось (это не JSON), пробуем определить тип.
        $obResultView = null;

        switch ($obFile->getMimeType()) {
            case "image/jpeg":
                $obResultView = $this->processJPEG($obFile);
                break;
            case "image/png":
                $obResultView = $this->processPNG($obFile);
                break;
            default:
                $obResultView = $this->processOther();
                break;
        }

        return $obResultView;
    }

    /**
     * Возвратить представление при ошибке загрузки.
     * @return View Представление с сообщением об ошибке.
     */
    private function uploadError()
    {
        return view("results")->with(
            [
                "bHasError" => true,
                "sResultMessage" => __("task1.upload_error")
            ]
        );
    }

    /**
     * Возвратить представление при превышении максимального размера файла.
     * @return View Представление с сообщением об ошибке.
     */
    private function fileSizeError()
    {
        return view("results")->with(
            [
                "bHasError" => true,
                "sResultMessage" => __("task1.file_size_exceed")
            ]
        );
    }

    /**
     * Обработать поступившие JSON-данные.
     * @param  array/object $obJSONData JSON-данные.
     * @return View                     Представление с сообщением о результате обработки.
     */
    private function processJSON($obJSONData)
    {
        return view("results")->with(
            [
                "bHasError" => false,
                "sResultMessage" => __("task1.file_processed"),
                "obJSONData" => $obJSONData
            ]
        );
    }

    /**
     * Обработать изображение в формате JPEG.
     * @param  UploadedFile $obFile Файл с изображением.
     * @return View                 Представление с сообщением о результате обработки.
     */
    private function processJPEG(UploadedFile $obFile)
    {
        $sFileName = $this->makeLocalFileName($obFile);

        $obImage = Image::make($obFile);

        // Для определения, надо ли понижать качество изображения, временно сохраняем
        // его с необходимым уровнем сжатия, а затем проверяем, уменьшился ли размер файла.
        $nInitialFilesize = $obImage->filesize();
        $obImage->save("upload/$sFileName.jpg", config("tasks.max_jpg_quality"));
        $nCompressedFilesize = $obImage->filesize();

        if ($nCompressedFilesize <= $nInitialFilesize) {
            // Если размер файла уменьшился, сохраняем сжатый вариант.
            Storage::disk("local")->put("images/$sFileName.jpg", $obImage);
        } else {
            // Если увеличился, значит, изображение уже было худшего качества,
            // и нет смысла его улучшать. Сохраняем исходный файл.
            $obFile->storeAs("images", $sFileName . ".jpg");
        }

        // Удаляем временный файл.
        unlink("upload/$sFileName.jpg");

        return view("results")->with(
            [
                "bHasError" => false,
                "sResultMessage" => __("task1.file_saved_as", ["filename" => $sFileName])
            ]
        );
    }

    /**
     * Обработать изображение в формате PNG.
     * @param  UploadedFile $obFile Файл с изображением.
     * @return View                 Представление с сообщением о результате обработки.
     */
    private function processPNG(UploadedFile $obFile)
    {
        $sFileName = $this->makeLocalFileName($obFile);

        $obImage = Image::make($obFile);

        // Конвертируем файл в JPG с заданным качеством и сохраняем его.
        $obImage = $obImage->encode("jpg", config("tasks.max_jpg_quality"));
        Storage::disk("local")->put("images/$sFileName.jpg", $obImage);

        return view("results")->with(
            [
                "bHasError" => false,
                "sResultMessage" => __("task1.file_converted_as", ["filename" => $sFileName])
            ]
        );
    }

    /**
     * Обработать файл иного типа.
     * @return View Представление с сообщением о результате обработки.
     */
    private function processOther()
    {
        return view("results")->with(
            [
                "bHasError" => true,
                "sResultMessage" => __("task1.unsupported_file_type")
            ]
        );
    }

    /**
     * Сформировать имя локального файла, который нужно сохранить.
     * @param  UploadedFile $obFile Файл, полученный сервером.
     * @return string               Название локального файла для сохранения.
     */
    private function makeLocalFileName(UploadedFile $obFile)
    {
        $sFileName = date("Y-m-d-H-i-s-");
        $sFileName .= pathinfo($obFile->getClientOriginalName(), PATHINFO_FILENAME);

        return $sFileName;
    }
}
