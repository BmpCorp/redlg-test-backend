<?php

namespace App\Http\Controllers;

use App\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TicketController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $obRequest
     * @return \Illuminate\Http\Response
     */
    public function store(Request $obRequest)
    {
        $obFile = $obRequest->file("file");

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

        dd($obTicket);
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

        File::makeDirectory(storage_path("app/$sPath"));
        Storage::disk("local")->put("$sPath/$sFileName", $obFile->get());

        return "$sPath/$sFileName";
    }
}
