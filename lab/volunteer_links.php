<?php

function getVolunteerLinks(): array
{
    $dir = "./volunteer_links/";
    $files = [];
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (filetype($dir . $file) === "file" &&
                    pathinfo($dir . $file, PATHINFO_EXTENSION) === "json" &&
                    str_starts_with($file, "diff_full_text")
                ) {
                    $y = substr($file, 14, 4);
                    $m = substr($file, 18, 2);
                    $d = substr($file, 20, 2);
                    $h = substr($file, 22, 2);
                    $i = substr($file, 24, 2);

                    $files[] = [
                      'name' => $file,
                      'date' => "{$y}年{$m}月{$d}日{$h}時{$i}分",
                      'csv' => pathinfo($dir . $file, PATHINFO_FILENAME) . ".csv",
                    ];
                }
            }
            closedir($dh);
        }
    }
    sort($files);
    return $files;
}


$files = json_encode(getVolunteerLinks());
?>
<html lang="">
<head>
  <title>令和6年能登半島地震更新チェック</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    const { createApp, ref } = Vue
    const app = createApp({
      setup() {
        const files = ref(<?php echo $files; ?>);
        const links = ref([])
        const getDetail = async function (file) {
          const response = await fetch('./volunteer_links/' + file);
          links.value = await response.json();

        }
        return {
          files,links,getDetail
        }
      }
    });
  </script>
  <style>
    body {
      font-size: 12px;
    }
    #link_list {
      border: solid 1px #000;
      border-collapse: collapse;
      margin:10px auto;
    }
    #link_list td,#link_list th {
      border: solid 1px #000;
    }
    .files {
      cursor: pointer;
      color: blue;
    }
  </style>
</head>
<body>
  <div id="app">
  <ul>
    <li v-for="file in files" @click="getDetail(file.name)" class="files">{{ file.date }}（<a :href="`/lab/volunteer_links/${ file.csv }`">CSV</a>）</li>
  </ul>
    <table id="link_list">
      <tr>
        <th>都道府県</th>
        <th>市区町村名</th>
        <th>種類</th>
        <th>URL</th>
        <th>updateタイプ</th>
        <th>差分全文</th>
      </tr>
      <tr v-for="row in links">
        <td>{{ row.都道府県 }}</td>
        <td>{{ row.市区町村名 }}</td>
        <td>{{ row.種類 }}</td>
        <td>{{ row.URL }}</td>
        <td>{{ row.update }}</td>
        <td>{{ row.update_text }}</td>
      </tr>
    </table>
  </div>
  <script>
    app.mount('#app')
  </script>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-37983895-3', 'miyawa-tarou.com');
    ga('send', 'pageview');

  </script>
</body>
</html>