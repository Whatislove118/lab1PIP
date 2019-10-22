<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Результат</title>
        <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
        <style>
            table{
                font-family: monospace;
                padding: 7px;
            }
            .time{
                color:blue;
            }
            #timedone{
                color:green;
            }

        </style>
    </head>
    <body>
    <?php
    date_default_timezone_set("Europe/Moscow");
    $request_time=date("H:i:s", time());
    $start_time=microtime(true);

    if(session_id()===""){
        session_start();
    }
    $arrayY=array();
    $checkX =true;
    $checkY=false;
    $checkR=true;
    $R=$_GET['result_button'];

    if($_GET['result_button']!=="Ваше выбранное значение:"){
        $R=$_GET['result_button'] ;
      $checkR=true;
    }
    if(!$checkR) {
        echo "Выберите координату R!<br>";
    }

    if($_GET['text_select']==="Введите ваше значение"){
        echo "Введите X!<br>";
        $checkX=false;
        $X=null;
    }else{
       $X=$_GET['text_select'].trim();
       if(!strcmp($X,"Введите ваше значение")){
           echo "Введите X!<br>";
           $checkX=false;
       } else {
           if(!is_numeric(str_replace(',','.',$_GET['text_select']))){
               echo "Некорректный ввод X!<br>";
               $checkX=false;
           }else{
               if(substr($_GET['text_select'],0,1)==='-'&&(float)str_replace(',', '.', $_GET['text_select'])==0){
                    $X=0;
               } else{
                   $X=(float)str_replace(',', '.', $_GET['text_select']);
                   if(($X <= -3) || ($X >=5)){
                        echo "X не принадлежит ОДЗ!<br>";
                        $checkX=false;
                   }
               }
           }
       }
    }
    //++
    for($j =1;$j <=9;$j++){
        if(isset($_GET['checkboxY'.$j])){
            array_push($arrayY,$_GET['checkboxY'.$j]);
            $checkY=true;
        }
    }
    if(!checkY){
        echo "Введите Y!";
    }


    if(!isset($_SESSION['points'])){
            $_SESSION['points']=array();
    }

    if($checkY && $checkX && $checkR){
        foreach ($arrayY as $valueY){
            $point = new Point($X,$R,$valueY, $request_time);
            array_push($_SESSION['points'],$point);
        }
    }
    echo "<table align='center'>
   <tr>
   <td><h1>Результаты:</h1></td>
    
    <td>Текущее время: <span class = 'time' id='time'></span></td>
    
    <td>Время запроса: <span class = 'time'>".$request_time."</span></td>
   
    <td>Время вычисления(с): <span class = 'time' id='timedone'></span></td>
    </tr>
    <tr>
     <td><hr/></td>
    <td><hr/></td>
    <td><hr/></td>
    <td><hr/></td>
    <td><hr/></td>
    </tr>
        
    <tr>
    <td>Координата Х</td>
    <td>Координата Y</td>
    <td>Радиус R</td>
    <td>Поподание</td>
    <td>Время</td>
    </tr>
    <td><hr/></td>
    <td><hr/></td>
    <td><hr/></td>
    <td><hr/></td>
    <td><hr/></td>
    </tr>";

    foreach (array_reverse($_SESSION['points']) as $point){
        echo "<tr>
        <td>$point->x</td>
        <td>$point->y</td>
         <td>$point->r</td>";
        echo $point->check()? "<td>Да</td>" : "<td>Нет</td>";
        echo "<td>$point->time</td>";
        echo "</tr>
                    <tr>
                    <td><hr/></td>
                    <td><hr/></td>
                    <td><hr/></td>
                    <td><hr/></td>
                    <td><hr/></td>
                    </tr>";
    }
    echo "</table>";

    $time = (float)round(microtime(true)-$start_time,6);
    if($time==0){
        $time= "Mеньше 0.0000001";
    }

    class Point
    {
        public $x;
        public $y;
        public $r;
        public $time;

        function __construct($X, $R, $Y, $time)
        {
            $this->x = $X;
            $this->y = $Y;
            $this->r = $R;
            $this->time = $time;
        }

        function check()
        {

            // + +
            if ($this->x >= 0 && $this->y >= 0) {
                if ($this->x <= $this->r/2 && $this->y <= $this->r) {
                    return true;
                } else {
                    return false;
                }
            }
            //+ -
            if ($this->x >= 0 && $this->y <= 0) {
                if ($this->x >= -$this->r / 2 && $this->y >= -$this->r / 2) {
                    return hypot($this->x, $this->y) <= $this->r / 2;
                } else {
                    return false;
                }
            }
            if ($this->x < 0 && $this->y > 0) {
                return false;
            }
            if ($this->x <= 0 && $this->y <= 0) {
                // xb yb =0 0  ; xa ya = -r/2 0; xc yc: 0 -r/2;   x0 y0: x y
                $checkPlus=(((-$this->r/2 - $this->x)*(0-0) - (0-$this->r/2)*(0-$this->y))>=0 && ((0-$this->x)*(-$this->r/2-0) -(0-0)*(0-$this->y))>=0 && ((0-$this->x)*(0+$this->r/2)-(-$this->r/2 -0)*(-$this->r/2 - $this->y))>=0);
                $checkMinus=(((-$this->r/2 - $this->x)*(0-0) - (0-$this->r/2)*(0-$this->y))<=0 && ((0-$this->x)*(-$this->r/2-0) -(0-0)*(0-$this->y))<=0 && ((0-$this->x)*(0+$this->r/2)-(-$this->r/2 -0)*(-$this->r/2 - $this->y))<=0);
                if($checkPlus | $checkMinus){
                    return true;
                }
            }
            return false;
        }
    }

    ?>
    <script>
        function show()
        {
            $.ajax({
                url: "/~s263156/PIP/LAB1/time.php",
                cache: false,
                success: function(html){
                    $("#time").html(html);
                }
            });
        }
        $(document).ready(function(){
            show();
            setInterval('show()',1000);
        });
        document.getElementById('timedone').innerHTML = '<?php echo $time;?>'
    </script>
    </body>
</html>