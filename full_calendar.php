<html>
<head>
  <link rel="stylesheet" href="/style.css" />
</head>
<body>
<pre>
<?php

class FileMap {
  public array $fileMap;
  private array $miscFiles;
  private int $miscFilePointer = 0;

  function __construct() {
    $this->fileMap = [];
    $files = array_filter(scandir('img/dated'), fn($n) => $n[0] !== '.');
    foreach ($files as $f) {
      $basename = explode('.', $f)[0];
      $this->fileMap[$basename] = $f;
    }

    $this->miscFiles = array_values(array_filter(scandir('img/misc'), fn($n) => $n[0] !== '.'));
    mt_srand(12345);
    shuffle($this->miscFiles);
  }

  public function getFilename(Day $day): string {
    $d = new DateTime("$day->month $day->day");
    $basename = $d->format('md');

    if (isset($this->fileMap[$basename])) {
      return 'img/dated/' . $this->fileMap[$basename];
    }

    return 'img/misc/' . $this->miscFiles[$this->miscFilePointer++];
  }
}

readonly class Day {
  public string $month;
  public int $day;
  public string $name;
  public string $line;

  function __construct(string $month, int $day, string $name, string $line) {
    $this->month = $month;
    $this->day = $day;
    $this->name = $name;
    $this->line = $line;
  }

  public function getColor(): string {
      return match ($this->month) {
        'January' => '#6bbcfa',
          'February' => '#f2c18d',
          'March' => '#c28b8c',
          'April' => '#09a892',
          'May' => '#9aed6a',
          'June' => '#c192e0',
          'July' => '#82d6f5',
          'August' => '#c1e67e',
          'September' => '#ebeb6c',
          'October' => '#fbb05f',
          'November' => '#c3c7ad',
          'December' => '#30b81c',
          default => '',
      };
  }
}

$fileMap = new FileMap();

$rows = [];
foreach (file('labels.csv') as $line) {
    $rows[] = str_getcsv($line);
}
array_shift($rows);

$allDays = array_map(fn($row) => new Day($row[0], $row[1], $row[2], $row[3]), $rows);
?>
</pre>

<?php foreach (array_chunk($allDays, 4) as $days): ?>
  <div class="days">
    <?php foreach ($days as $day): ?>
      <div class="day" style="background-color:<?php echo $day->getColor(); ?>;">
        <div class="pic">
          <img src="<?php echo $fileMap->getFilename($day); ?>" />
        </div>
        <div class="date"><?php echo $day->month; ?> <span class="num"><?php echo $day->day; ?></span></div>
        <div class="txt">
          <span class="dayname"><?php echo $day->name; ?></span>
          <span class="line"><?php echo $day->line; ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>
</body>
</html>
