<?php

namespace App\Imports;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Shop;

class ShopImport
{
    protected $file;
    protected $validRows = [];
    protected $errorMessages = [];
    protected $header = [];
    protected $headerCount = 0;

    protected $rules = [
        'name'        => 'required|string|max:50',
        'area'        => 'required|in:東京都,大阪府,福岡県',
        'genre'       => 'required|in:寿司,焼肉,イタリアン,居酒屋,ラーメン',
        'description' => 'required|string|max:400',
        'image_url'   => ['required', 'url', 'regex:/\.(jpg|jpeg|png)$/i'],
    ];

    protected $areaMap  = ['東京都' => 1, '大阪府' => 2, '福岡県' => 3];
    protected $genreMap = ['寿司' => 1, '焼肉' => 2, 'イタリアン' => 3, '居酒屋' => 4, 'ラーメン' => 5];

    protected $customMessages;

    /**
     *
     *
     * @param \Illuminate\Http\UploadedFile
     * @param array
     */
    public function __construct($file, $customMessages = [])
    {
        $this->file = $file;
        $this->customMessages = $customMessages;
    }

    /**
     *
     *
     * @return array
     * @throws \Exception
     */
    public function import()
    {
        $rows = $this->getCsvRows();
        if ($rows === false) {
            throw new \Exception('CSVファイルの読み込みに失敗しました');
        }
        if (empty($rows)) {
            throw new \Exception('CSVファイルにデータがありません');
        }

        $this->setHeader($rows[0]);

        for ($i = 1; $i < count($rows); $i++) {
            $rowNumber = $i + 1;
            $this->validateRow($rows[$i], $rowNumber);
        }

        if (!empty($this->validRows)) {
            Shop::insert($this->validRows);
        }

        return [
            'imported_count' => count($this->validRows),
            'error_messages' => $this->errorMessages,
        ];
    }

    /**
     *
     *
     * @return array|false
     */
    protected function getCsvRows()
    {
        if (($handle = fopen($this->file->getRealPath(), 'r')) !== false) {
            $rows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
            return $rows;
        }
        return false;
    }

    /**
     *
     *
     * @param array
     * @throws \Exception
     */
    protected function setHeader($headerRow)
    {
        $header = array_map('trim', $headerRow);
        $header = array_map('strtolower', $header);
        $expectedHeader = ['name', 'area', 'genre', 'description', 'image_url'];

        if (count(array_intersect($expectedHeader, $header)) !== count($expectedHeader)) {
            throw new \Exception('CSVのヘッダーに必要なカラムが含まれていません');
        }
        $this->header = $header;
        $this->headerCount = count($header);
    }

    /**
     *
     *
     * @param array
     * @param int
     */
    protected function validateRow($rawRow, $rowNumber)
    {
        $originalCount = count($rawRow);
        if ($originalCount !== $this->headerCount) {
            $this->errorMessages[] = "行 {$rowNumber}: カラム数が一致しません、その行に {$this->headerCount} 個のカラムが必要です";
            return;
        }

        $paddedRow = array_pad($rawRow, $this->headerCount, '');
        $rowAssoc = array_combine($this->header, $paddedRow);

        $validator = Validator::make($rowAssoc, $this->rules, $this->customMessages);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $this->errorMessages[] = "行 {$rowNumber}: " . implode('、 ', $errors);
            return;
        }

        try {
            $imageUrl = $rowAssoc['image_url'];
            $imageContents = file_get_contents($imageUrl);
            $imageName = Str::random(20) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
            Storage::disk('public')->put('images/' . $imageName, $imageContents);
            $localImageUrl = Storage::url('images/' . $imageName);
        } catch (\Exception) {
            $this->errorMessages[] = "行 {$rowNumber}: 画像の取得に失敗しました";
            return;
        }

        $this->validRows[] = [
            'user_id'     => 1,
            'name'        => $rowAssoc['name'],
            'area_id'     => $this->areaMap[$rowAssoc['area']] ?? null,
            'genre_id'    => $this->genreMap[$rowAssoc['genre']] ?? null,
            'description' => $rowAssoc['description'],
            'image_url'   => $localImageUrl,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
