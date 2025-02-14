<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ], [
            'csv_file.required' => 'CSVファイルは必須です。',
            'csv_file.file'     => '有効なファイルをアップロードしてください。',
            'csv_file.mimes'    => 'CSVまたはTXTファイルのみアップロード可能です。',
        ]);

        $file = $request->file('csv_file');
        $rows = [];
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        } else {
            return redirect()->back()->with('error', 'CSVファイルの読み込みに失敗しました。');
        }

        if (empty($rows)) {
            return redirect()->back()->with('error', 'CSVファイルにデータがありません。');
        }

        $header = array_map('trim', $rows[0]);

        $expectedHeader = ['name', 'area', 'genre', 'description', 'image_url'];

        $header = array_map('strtolower', $header);
        if (count(array_intersect($expectedHeader, $header)) !== count($expectedHeader)) {
            return redirect()->back()->with('error', 'CSVのヘッダーに必要なカラムが含まれていません。');
        }
        $headerCount = count($header);

        $rules = [
            'name'        => 'required|string|max:50',
            'area'        => 'required|in:東京都,大阪府,福岡県',
            'genre'       => 'required|in:寿司,焼肉,イタリアン,居酒屋,ラーメン',
            'description' => 'required|string|max:400',
            'image_url'   => ['required', 'url', 'regex:/\.(jpg|jpeg|png)$/i'],
        ];

        $customMessages = [
            'name.required'        => '店舗名は必須です。',
            'name.max'             => '店舗名は50文字以内で入力してください。',
            'area.required'        => '地域は必須です。',
            'area.in'              => '地域は「東京都」「大阪府」「福岡県」から選択してください。',
            'genre.required'       => 'ジャンルは必須です。',
            'genre.in'             => 'ジャンルは「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」から選択してください。',
            'description.required' => '店舗概要は必須です。',
            'description.max'      => '店舗概要は400文字以内で入力してください。',
            'image_url.required'   => '画像URLは必須です。',
            'image_url.url'        => '画像URLは正しいURL形式で入力してください。',
            'image_url.regex'      => '画像URLはjpg、jpeg、png形式のみアップロード可能です。',
        ];

        $errorMessages = [];
        $validRows = [];

        for ($i = 1; $i < count($rows); $i++) {
            $rowNumber = $i + 1;
            $rawRow = $rows[$i];
            $originalCount = count($rawRow);

            if ($originalCount !== $headerCount) {
                $errorMessages[] = "行 {$rowNumber}: カラム数が一致しません。その行に {$headerCount} 個のカラムが必要です。";
                continue;
            }

            $paddedRow = array_pad($rawRow, $headerCount, '');
            $rowAssoc = array_combine($header, $paddedRow);

            $validator = Validator::make($rowAssoc, $rules, $customMessages);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $errorMessages[] = "行 {$rowNumber}: " . implode($errors);
            } else {
                $areaMap  = ['東京都' => 1, '大阪府' => 2, '福岡県' => 3];
                $genreMap = ['寿司' => 1, '焼肉' => 2, 'イタリアン' => 3, '居酒屋' => 4, 'ラーメン' => 5];

                $imageUrl = $rowAssoc['image_url'];

                $imageContents = file_get_contents($imageUrl);
                $imageName = Str::random(20) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                Storage::disk('public')->put('images/' . $imageName, $imageContents);
                $localImageUrl = Storage::url('images/' . $imageName);

                $validRows[] = [
                    'user_id'     => 1,
                    'name'        => $rowAssoc['name'],
                    'area_id'     => $areaMap[$rowAssoc['area']] ?? null,
                    'genre_id'    => $genreMap[$rowAssoc['genre']] ?? null,
                    'description' => $rowAssoc['description'],
                    'image_url'   => $localImageUrl,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        if (!empty($validRows)) {
            Shop::insert($validRows);

            if (!empty($errorMessages)) {
                return redirect()->back()->with('success', '正常に ' . count($validRows) . ' 件の行をインポートしました。<br>以下の行はエラーのためインポートされませんでした。<br>' . implode('<br>', $errorMessages));
            } else {
                return redirect()->back()->with('success', '全ての行のインポートに成功しました。');
            }
        }

        return redirect()->back()->with('error', '全ての行に入力不備があるか、インポートするデータがありません。');
    }
}
