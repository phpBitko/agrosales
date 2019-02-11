<?php


namespace AppBundle\Service;
use Symfony\Component\HttpFoundation\Request;

class ParseFilterServices
{
    /**
     * Парсить строку url з параметрами, та готує дані для відображення параметрів фільтрації
     *
     * @param Request $request
     * @return array
     */
    public function parseQueryString(Request $request)
    {
        $filterParams = $request->query->get('item_filter');

        $resultFilter = [];

        if ($filterParams !== null) {

            $queryParamArray = [];

            foreach ($filterParams as $k => $v) {

                $queryParamArray['item_filter'] = $filterParams;
                unset($queryParamArray['item_filter'][$k]);

                if (is_array($v) ) {
                    $resultFilter[$k] = $v;
                    $str = http_build_query($queryParamArray);
                    $resultFilter[$k]['strHref'] = $str;
                } else {
                    $str = http_build_query($queryParamArray);
                    $resultFilter[$k]['param'] = 1;
                    $resultFilter[$k]['strHref'] = $str;
                }

            }

            $resultFilter = $this->parseResultFilter($resultFilter);
        }
        return $resultFilter;
    }


    /**
     * Обрабляє масив для відображення параметрів фільтрації, додає строку з даними фільтра
     *
     * @param array $arrayFilter
     * @return array
     */
    protected function parseResultFilter(array $arrayFilter)
    {
        if ($arrayFilter !== null) {
            $arrCopy = $arrayFilter;
            $str = '';

            foreach ($arrayFilter as $k => $v) {
                switch ($k) {
                    case 'price':
                        if (!empty($arrayFilter[$k]['left_number']) || !empty($arrayFilter[$k]['right_number'])) {
                            $str = 'ціна ';
                            $str .= (!empty($v['left_number'])) ? 'від: ' . $v['left_number'] . ' ' : '';
                            $str .= (!empty($v['right_number'])) ? 'до: ' . $v['right_number'] : '';
                            $arrCopy[$k]['strText'] = $str;
                        }
                        break;
                    case 'area':
                        if (!empty($arrayFilter[$k]['left_number']) || !empty($arrayFilter[$k]['right_number'])) {
                            $str = 'площа ';
                            $str .= (!empty($v['left_number'])) ? 'від: ' . $v['left_number'] . ' ' : '';
                            $str .= (!empty($v['right_number'])) ? 'до: ' . $v['right_number'] : '';
                            $arrCopy[$k]['strText'] = $str;
                        }
                        break;
                    case 'addDate':
                        if (!empty($arrayFilter[$k]['left_datetime']) || !empty($arrayFilter[$k]['right_datetime'])) {
                            $str = 'дата ';
                            $str .= (!empty($v['left_datetime'])) ? 'від: ' . $v['left_datetime'] . ' ' : '';
                            $str .= (!empty($v['right_datetime'])) ? 'до: ' . $v['right_datetime'] : '';
                            $arrCopy[$k]['strText'] = $str;
                        }
                        break;
                    case 'isRoad':
                        $arrCopy[$k]['strText'] = 'є дорога';
                        break;
                    case 'isWaterSupply':
                        $arrCopy[$k]['strText'] = 'є вода';
                        break;
                    case 'isElectricity':
                        $arrCopy[$k]['strText'] = 'є електрика';
                        break;
                    case 'isGas':
                        $arrCopy[$k]['strText'] = 'є газ';
                        break;
                    case 'isSewerage':
                        $arrCopy[$k]['strText'] = 'є каналізація';
                        break;
                    case 'dirPurpose':
                        $arrCopy[$k]['strText'] = 'цільове призначення';
                        break;
                }
            }
            return $arrCopy;
        }
    }

}