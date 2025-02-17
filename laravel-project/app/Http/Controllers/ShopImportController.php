<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopImportRequest;
use App\Imports\ShopImport;

class ShopImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.import');
    }

    public function import(ShopImportRequest $request)
    {
        try {
            $shopImport = new ShopImport(
                $request->file('csv_file'),
                (new ShopImportRequest)->messages()
            );

            $result = $shopImport->import();
            $importedCount = $result['imported_count'];
            $errorMessages = $result['error_messages'];

            if ($importedCount > 0) {
                $message = '正常に ' . $importedCount . ' 件の行をインポートしました。';
                if (!empty($errorMessages)) {
                    $message .= '<br>以下の行はエラーのためインポートされませんでした。<br>' . implode('<br>', $errorMessages);
                }
                return redirect()->back()->with('success', $message);
            }

            return redirect()->back()->with('error', '全ての行に入力不備があるか、インポートするデータがありません。');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
