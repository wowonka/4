<?
require_once($_SERVER['DOCUMENT_ROOT'] . "./bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}
\Bitrix\Main\Loader::includeModule('iblock');
$row = 1;
$IBLOCK_ID = 12;

$el = new CIBlockElement;

$rsProp = CIBlockPropertyEnum::GetList(
    ["SORT" => "ASC", "VALUE" => "ASC"],
    ['IBLOCK_ID' => $IBLOCK_ID]
);
while ($arProp = $rsProp->Fetch()) {
    $key = trim($arProp['VALUE']);
    $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
}


if (($handle = fopen("barselona.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if ($row == 1) {
            $row++;
            continue;
        }
        $row++;        

        $PROP['NAME'] = $data[1];
        $PROP['AMPLUA'] = $data[2];
        
        
        foreach ($PROP as $key => &$value) {            
            if ($arProps[$key]) {                
                foreach ($arProps[$key] as $propKey => $propVal) {                    
                    if (stripos($propKey, $value) !== false) {
                        $value = $propVal;
                        break;
                    }                    
                }                
            }
        }
        $arLoadProductArray = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $data[1],
            "ACTIVE" => end($data) ? 'Y' : 'N',
        ];

        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            echo "???????? ??????? ? ID : " . $PRODUCT_ID . "<br>";
        } else {
            echo "Error: " . $el->LAST_ERROR . '<br>';
        }
    }
    
    fclose($handle);
}
?>