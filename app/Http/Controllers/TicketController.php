<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Mail\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Сохранить заявку в БД и направить уведомление на электронную почту.
     *
     * @param  Request  $obRequest Данные поступившего запроса.
     * @return Response
     */
    public function store(Request $obRequest)
    {
        if (!request("name") || !request("email")) {
            return $this->uploadError("task2.required_data_missing");
        }

        $obFile = $obRequest->file("file");

        // Если в заявке присутствует файл, выполняем необходимые проверки
        // и сохраняем его на сервере, а в БД записываем ссылку на него.
        if ($obFile) {
            if(!$obFile->isValid()) {
                return $this->uploadError("task2.invalid_file");
            }

            if ($obFile->getSize() > config("tasks.max_attachment_size")) {
                return $this->uploadError("task2.file_size_exceed");
            }

            $sFileType = $obFile->getMimeType();

            if(!in_array($sFileType, config("tasks.allowed_doc_types"))) {
                return $this->uploadError("task2.doc_expected");
            }

            $sFileLink = $this->storeFile($obFile);
        }

        $obTicket = Ticket::create(request(["name", "email", "phone"]));
        if ($obFile) {
            $obTicket["file_link"] = $sFileLink;
            $obTicket->save();
        }

        $this->sendNotification($obTicket);

        return view("results")->with(
            [
                "bHasError" => false,
                "sResultMessage" => __("task2.ticket_created")
            ]
        );
    }

    /**
     * Возвратить представление при ошибке загрузки.
     * @param string $sErrorMessage Строка, определяющая сообщение об ошибке.
     * @return View Представление с сообщением об ошибке.
     */
    private function uploadError($sErrorMessage)
    {
        return view("results")->with(
            [
                "bHasError" => true,
                "sResultMessage" => __($sErrorMessage)
            ]
        );
    }

    /**
     * Сохранить файл, приложенный к заявке, на сервере.
     * @param  UploadedFile $obFile Приложенный файл.
     * @return string               Путь к сохранённому файлу на сервере.
     */
    private function storeFile(UploadedFile $obFile)
    {
        $sPath = "documents/". date("Y-m-d-H-i-s");
        $sFileName = $obFile->getClientOriginalName();

        // Каждый файл сохраняется под оригинальным именем в отдельной папке.
        File::makeDirectory(storage_path("app/$sPath"));
        Storage::disk("local")->put("$sPath/$sFileName", $obFile->get());

        return "$sPath/$sFileName";
    }

    /**
     * Отправить уведомление о поступившей заявке.
     * @param  Ticket $obTicket Заявка.
     * @return void
     */
    private function sendNotification(Ticket $obTicket)
    {
        $obMail = new Notification($obTicket);
        Mail::to(config("tasks.mail_to"))->send($obMail);
    }
}
