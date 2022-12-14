# Crawling-and-Searching-Data-at-Detik.com

Repository ini merupakan hasil dari project yang telah saya kerjakan yaitu membuat web crawling dan searching pada website detik.com.

#CRAWLING & #SEARCHING

  Crawling adalah sebuah cara seperti yang digunakan menyerupai sistem pencarian atau search engine dengan melakukan pemindaian pada setiap konten pada sebuah website. 
Proses yang dilakukan pada sistem crawling yang saya buat yaitu sistem dapat mencari secara otomatis link setiap konten pada website detik.com. kemudian hasil dari pencarian link tersebut saya simpan di database. Proses ini juga membutuhkan sebuah metode, metode yang saya gunakan adalah metode sastrawi. banyak metode lain yang dapat melakukan crawling selain sastrawi. 

  Namun alasan saya menggunakan sastrawi dikarenakan proses crawling adalah mencakup data yang besar dan banyak sehingga banyak judul dari konten yang menggunakan kata imbuhan. Oleh karena itu sastrawi memiliki kelebihan dalam stemming dan stopword sehingga dapat menguraikan kata yang menggunakan imbuhan menjadi kata tanpa imbuhan atau disebut kata dasar. Output dari proses crawling tersebut sistem dapat menemukan dan menyimpan judul serta link dari konten, kemudian output lainnya yaitu judul dari konten yang tadinnya menggunakan kata ber-imbuhan sehingga di uraikan dan menghasilkan kata yang tanpa imbuhan. 

  Searching adalah cara untuk melakukan pencarian. pencarian yang dimaksud pada proses ini adalah melakukan similarity antara hasil pencarian yang dilakukan dengan proses crawl dan proses searh. Proses search pada sistem ini menggunakan metode php-ml, Hamming dan Dice. Metode php-ml digunakan untuk memberikan bobot nilai awal pada nilai TF Binary dan TF-IDF atau nilai TF Raw. Metode php-ml melakukan pembobotan nilai pada hasil data clean atau kalimat yang tidak memiliki kata ber-imbuhan. kemudian hasil dari pemobobotan nilai tersebut dilakukan perhitungan similaritas dengan menggunakan metode Hamming dan Dice. Hasil dari proses search berupa table yang terdapat kolom title yang berisi data cleanning, kolom link dari konten berita, selanjutnya nilai dari hasil perhitungan similaritas.

Bite Size Make Perfect.
