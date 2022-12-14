<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Search Menggunakan Metode Hamming dan Dice Dengan Menghitung Total Similarity</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/sl-slide.css">
    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>

<body>
    <!--Header-->
    <header class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a id="logo" class="pull-left" href="index.php"></a>
                <div class="nav-collapse collapse pull-right">
                    <ul class="nav">
                        <li class="active"><a href="index.php">Crawling Data</a></li>
                        <li><a href="search.php">Searching</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <!-- /header -->

    <!--Services-->
    <section id="services">
        <div class="container">
            <div class="center gap">
                <h3>News Search</h3>
                <form method="POST" action="">
                    <p class="lead">Input Keyword <input type="text" name="keyword">
                        <input type="submit" name="search" value="Search"><br>
                        <b>
                            <font size="4">Choose Similarity Method :
                        </b></font>
                        <input type="radio" name="method" value="Hamming"> Hamming
                        <input type="radio" name="method" value="Dice"> Dice <br>
                    </p>
                </form>

            <?php
                require_once __DIR__ . '/vendor/autoload.php';

                use Phpml\FeatureExtraction\TokenCountVectorizer;
                use Phpml\Tokenization\WhitespaceTokenizer;
                use Phpml\FeatureExtraction\TfIdfTransformer;

                

                if (isset($_POST['search'])) 
                {
                    $sample_data = [];
                    $title = [];
                    $tf_binary = [];
                    $i = 0;

                    $con = $mysqli = new mysqli("localhost", "root", "", "news");
                    $sql_select = "SELECT title, clean_data FROM contents";
                    $result = mysqli_query($con, $sql_select);

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $sample_data[$i] = $row["clean_data"];
                            $title[$i] = $row["title"];
                            $i++;
                        }
                        $sample_data[$i] = $_POST["keyword"];
                    }
                    else echo "Data Still Empty. Please Crawling First";

                    $tf = new TokenCountVectorizer(new WhitespaceTokenizer());
                    $tf->fit($sample_data);
                    $tf->transform($sample_data);

                    for($i=0;$i<count($sample_data);$i++){
                        for($x=0;$x<count($sample_data[$i]);$x++){
                            if($sample_data[$i][$x] > 0) $tf_binary[$i][$x] = 1;
                            else $tf_binary[$i][$x] = 0;
                        }
                    }
                    $tfidf = new TfIdfTransformer($sample_data);
                    $tfidf->transform($sample_data);

                    if ($_POST['method'] == 'Hamming') {
                        $query_idx = count($tf_binary)-1;
                        for($i=0; $i<$query_idx; $i++) {
                            $result = 0;

                            for ($x = 0; $x < count($tf_binary[$i]); $x++) {
                                if ($tf_binary[$query_idx][$x] == $tf_binary[$i][$x]) continue;
                                else $result += 1;
                            }
                            $sql = 'UPDATE contents SET similarity = "'.$result.'" WHERE title = "'.$title[$i].'"';
                            mysqli_query($con, $sql);
                        }
                    } 
                        else{
                            $query_idx = count($tf_binary)-1;
                            for ($i=0; $i<$query_idx; $i++) {
                                $similarity = 0.0;
                                $denom_wkq = 0.0;
                                $denom_wkj = 0.0;
                                $numerator = 0.0;
                                $denum = 0.0;

                                for ($x = 0; $x < count($sample_data[$i]); $x++) {
                                    $numerator += $sample_data[$query_idx][$x] * $sample_data[$i][$x];
                                    $denom_wkq += pow($sample_data[$query_idx][$x], 2);
                                    $denom_wkj += pow($sample_data[$i][$x], 2);
                                }
                                if($denom_wkq == 0.0 && $denom_wkj == 0.0)$result = 0.0;
                                else $result = $numerator / (0.5 * ($denom_wkq + $denom_wkj));

                                $sql = 'UPDATE contents SET similarity= "'.round($result, 2).'" WHERE title = "'.$title[$i].'"';
                                mysqli_query($con, $sql);
                            }
                        }
                        echo "<table border='1'>";
                        echo "<tr>";
                        echo "<th align='center'>Title</th>";
                        echo "<th align='center'>Link</th>";
                        echo "<th align='center'>Similarity</th>";
                        echo "</tr>";
                        if($_POST["method"]== "Hamming") $sql_select = "SELECT title, link, similarity FROM contents ORDER BY similarity asc";
                        else $sql_select = "SELECT title, link, similarity FROM contents ORDER BY similarity desc";

                        $result = mysqli_query($con, $sql_select);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>".$row["title"]."</td>";
                                echo "<td>".$row["link"]."</td>";
                                echo "<td>".$row["similarity"]."</td>";
                                echo "</tr>";
                            }
                        }
                        echo "</table>";
                        mysqli_close($con);
                }
            
            ?>    
            </div>
        </div>
    </section>
    <!--/Services-->

    <!--Footer-->
    <footer id="footer">
        <div class="container">
            <div class="row-fluid">
                <div class="span5 cp">
                    &copy; 2016 <a target="_blank" href="http://www.domain.com/">Your Company</a>. All Rights Reserved.
                </div>
                <div class="span6">
                    <ul class="social pull-right">
                        <li><a href="#"><i class="icon-facebook"></i></a></li>
                        <li><a href="#"><i class="icon-twitter"></i></a></li>
                        <li><a href="#"><i class="icon-pinterest"></i></a></li>
                        <li><a href="#"><i class="icon-linkedin"></i></a></li>
                        <li><a href="#"><i class="icon-google-plus"></i></a></li>
                        <li><a href="#"><i class="icon-youtube"></i></a></li>
                        <li><a href="#"><i class="icon-instagram"></i></a></li>
                    </ul>
                </div>
                <div class="span1">
                    <a id="gototop" class="gototop pull-right" href="#"><i class="icon-angle-up"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <!--/Footer-->
</body>

</html>