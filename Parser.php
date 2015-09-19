<?php
namespace pdllbq\phcparser;

use Yii;

class Parser
{
	private static $url='http://proxylist.hidemyass.com/'; //Адрес страницы с proxy

	//Получаем список прокси с сайта
	public static function getProxy()
	{
		return self::parse();
	}	
	
	//Получаем страницу с proxy
	private static function getPage()
	{
		$data=Yii::$app->cache->get('dfox.proxylistHidemyassComParser.Parse.getPage');
		if($data){
			return $data;
		}
		
		$ch = curl_init(self::$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		$data=curl_exec($ch);
		curl_close($ch);

		Yii::$app->cache->set('dfox.proxylistHidemyassComParser.Parse.getPage',$data,3600);

		return $data;
	}

	//Ищем скрытые классы
	private static function findHiddenClass($html)
	{
		//.uSYX{display:none}
		preg_match_all('/\.([A-Za-z0-9-]{4}){display:none}/sim',$html,$out);

		return $out[1];
	}

	//Отбрасываем лишние из массива c прокси
	private static function normalaizArray($array)
	{
		$data=[];
		$n=0;
		$index=-1;
		$count=count($array);
		for($i=0; $i<$count; $i++){
			if($n==0){
				$index++;
				$data[$index]=$array[$i];
			}elseif($n==1){
				$data[$index].=':'.$array[$i];
			}
			$n++;
			if($n==5){
				$n=0;
			}
		}

		return $data;
	}

	//Удаляем то что не похоже на прокси
	private static function deleteNonProxy($array)
	{
		$data=[];
		
		foreach($array as $value){
			$pm=preg_match('/^(\d{1,4})\.(\d{1,4})\.(\d{1,4})\.(\d{1,4}):(\d{1,4})$/',$value,$out);
			if($pm==1){
				$data[]=$value;
			}
		}
		return $data;
	}

	//Парсим прокси
	private static function parse()
	{
		$html=self::getPage();

		$hiddenClasses=self::findHiddenClass($html);

		//Убираем скрытые span
		$html=preg_replace('/<span style="display:none">(\d+)<\/span>/sim','',$html);
		$html=preg_replace('/<div style="display:none">(\d+)<\/div>/sim','',$html);

		foreach($hiddenClasses as $value){
			$html=preg_replace('/<span class="'.$value.'">(\d+)<\/span>/sim','',$html);
			$html=preg_replace('/<div class="'.$value.'">(\d+)<\/div>/sim','',$html);
		}

		preg_match_all('/<td>(.*?)<\/td>/sim',$html,$out);
		$result=[];
		foreach($out[1] as $value){
			//$d=preg_replace('/<span><style>(.*?)<\/style><span><\/span>/sim','',$value);
			$d=preg_replace('/<style>(.*?)<\/style>/sim','',$value);
			$d=preg_replace('/<span class="(.*?)">/sim','',$d);
			$d=preg_replace('/<div class="(.*?)">/sim','',$d);
			$d=str_replace('<span>','',$d);
			$d=str_replace('</span>','',$d);
			$d=str_replace('<div>','',$d);
			$d=str_replace('</div>','',$d);
			$d=str_replace('<span style="display: inline">','',$d);
			$d=str_replace(' ','',$d);
			$d=str_replace("\r",'',$d);
			$d=str_replace("\n",'',$d);
			$result[]=$d;
			
		}

		$result=self::normalaizArray($result);
		$result=self::deleteNonProxy($result);
		
		return $result;
	}
}
