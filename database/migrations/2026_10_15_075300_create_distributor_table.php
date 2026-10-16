<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('distributor', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255)->nullable();
            $table->string('alamat1', 255)->nullable();
            $table->string('alamat2', 255)->nullable();
            $table->string('alamat3', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('telp', 50)->nullable();
            $table->string('search_key', 50)->nullable()->index();
            $table->string('searchkey', 50)->nullable();
            $table->timestamps();
        });

        DB::table('distributor')->insert([
            ['id' => 1, 'nama' => 'PT. Mensa Bina Sukses - JAKARTA 1', 'alamat1' => 'Pulo Kambing II/26', 'alamat2' => 'Kawasan Industri Pulogadung', 'alamat3' => 'Jakarta Timur - 13930', 'kota' => 'JAKARTA -1', 'telp' => '021-4615906', 'search_key' => 'JKT01', 'searchkey' => 'JKT01'],
            ['id' => 2, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl.Kamal Muara IX  RT 003 RW 002', 'alamat2' => 'Kel. Kamal Muara, Kec Penjaringan', 'alamat3' => 'Kota Administrasi Jakarta Utara', 'kota' => 'JAKARTA -2', 'telp' => '021-29030504', 'search_key' => 'JKT02', 'searchkey' => 'JKT02'],
            ['id' => 3, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Menpor No.9 RT10 RW03 Palsigunung', 'alamat2' => 'Kel. Tugu, Kec. Cimanggis, Depok', 'alamat3' => 'Telp. 021-29833939', 'kota' => 'JAKARTA -3', 'telp' => '021-29833939', 'search_key' => 'JKT03', 'searchkey' => 'JKT03'],
            ['id' => 4, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Sinar Hati Raya No. 88 RT 004/RW 002', 'alamat2' => 'Kel. Sukajadi Kec. Karawaci', 'alamat3' => 'Tangerang', 'kota' => 'TANGERANG', 'telp' => '021-55732329', 'search_key' => 'JKT04', 'searchkey' => 'JKT04'],
            ['id' => 5, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Mojo Klanggru No. 227', 'alamat2' => 'RT. 06 / 04', 'alamat3' => 'Surabaya 60285', 'kota' => 'SURABAYA', 'telp' => '031-5914456', 'search_key' => 'SBY', 'searchkey' => 'SBY'],
            ['id' => 6, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Pandalandung No 47', 'alamat2' => 'Kecamatan Wagir Kabupaten Malang', 'alamat3' => '65158', 'kota' => 'MALANG', 'telp' => '0341-5029999', 'search_key' => 'MLG', 'searchkey' => 'MLG'],
            ['id' => 7, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Pamularsih No. 106', 'alamat2' => 'Semarang', 'alamat3' => '50111', 'kota' => 'SEMARANG', 'telp' => '024-7626140', 'search_key' => 'SMG', 'searchkey' => 'SMG'],
            ['id' => 8, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Kolonel Sutarto No.100', 'alamat2' => 'Jebres', 'alamat3' => 'Surakarta - Solo', 'kota' => 'SOLO', 'telp' => '0271-666770', 'search_key' => 'SLO', 'searchkey' => 'SLO'],
            ['id' => 9, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Komplek Papuri', 'alamat2' => 'Jalan Soekarno Hatta No. 785 A', 'alamat3' => 'Bandung 40284', 'kota' => 'BANDUNG', 'telp' => '022-63734422', 'search_key' => 'BDG', 'searchkey' => 'BDG'],
            ['id' => 10, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Mohammad Ikhwan Ridwan Rais No. 25', 'alamat2' => 'Kel. Kedamaian Kec. Tanjungkarang Timur', 'alamat3' => 'Bandar Lampung', 'kota' => 'B. LAMPUNGDFA', 'telp' => '0721-266028', 'search_key' => 'LPG', 'searchkey' => 'LPG'],
            ['id' => 11, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Tempua No. 36', 'alamat2' => 'Kel. Sei Seikambing B Kec. Medan Sunggal', 'alamat3' => 'Medan - 20122', 'kota' => 'MEDAN', 'telp' => '061-42008266', 'search_key' => 'MDN', 'searchkey' => 'MDN'],
            ['id' => 12, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Letjen Harun Sohar', 'alamat2' => 'Komp. Pergudangan Palembang Star Blok B 07,Tanjung api-api KM 1.3', 'alamat3' => 'Palembang 30152', 'kota' => 'PALEMBANG', 'telp' => '0711-421950', 'search_key' => 'PLB', 'searchkey' => 'PLB'],
            ['id' => 13, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Ir. Sutami No 19A RT01 RW05', 'alamat2' => 'kelurahan Bira', 'alamat3' => 'Makassar 90244', 'kota' => 'MAKASSAR', 'telp' => '0411-511895', 'search_key' => 'MKS', 'searchkey' => 'MKS'],
            ['id' => 14, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Gunung Agung No. 161', 'alamat2' => null, 'alamat3' => 'Denpasar - 80118', 'kota' => 'DENPASAR', 'telp' => '0361-9005530', 'search_key' => 'DPS', 'searchkey' => 'DPS'],
            ['id' => 15, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. A. Yani KM 13.300 RT008/003', 'alamat2' => 'Kel. Gambut barat, Kec. Gambut, Kab. Banjar', 'alamat3' => 'Banjarmasin', 'kota' => 'BANJARMASIN', 'telp' => '0511-4220193', 'search_key' => 'BMS', 'searchkey' => 'BMS'],
            ['id' => 16, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Arengka II ( Jl. S.M. Amin )', 'alamat2' => 'Kompleks Pergudangan Angkasa Blok C no. 1', 'alamat3' => 'Pekanbaru - Riau', 'kota' => 'PEKANBARU', 'telp' => '0761-7875510', 'search_key' => 'PKB', 'searchkey' => 'PKB'],
            ['id' => 17, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Interchange Manado Bitung', 'alamat2' => 'Desa Maumbi Jaga IV, Kec. Kalawat', 'alamat3' => 'Minahasa Utara', 'kota' => 'MANADO', 'telp' => null, 'search_key' => 'MNO', 'searchkey' => 'MNO'],
            ['id' => 18, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jln. Ir. Sutami Komplex Pergudangan Blok C.8 No  11-13', 'alamat2' => 'Kel. Karang Asam Ulu Kec. Sungai Kunjang', 'alamat3' => 'Samarinda 75126', 'kota' => 'SAMARINDA', 'telp' => '0541-7092822', 'search_key' => 'SMD', 'searchkey' => 'SMD'],
            ['id' => 19, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Raya By Pass KM 9 Taratak Paneh RT 02 / RW 06', 'alamat2' => 'Kel. Korong Gadang Kec. Kuranji', 'alamat3' => 'PADANG - 25156', 'kota' => 'PADANG', 'telp' => '0751-495055', 'search_key' => 'PDG', 'searchkey' => 'PDG'],
            ['id' => 20, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. M. Sohor No. 6A / 8A', 'alamat2' => 'RT 4 RW 18', 'alamat3' => 'Pontianak 78121', 'kota' => 'PONTIANAK', 'telp' => '0561-7051928', 'search_key' => 'PTK', 'searchkey' => 'PTK'],
            ['id' => 21, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Buyut No. 28 D', 'alamat2' => 'Kutasirap - Pegambiran', 'alamat3' => 'Cirebon', 'kota' => 'CIREBON', 'telp' => '0231-220461', 'search_key' => 'CRB', 'searchkey' => 'CRB'],
            ['id' => 22, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Pura No. 379', 'alamat2' => 'Plumbon - Banguntapan', 'alamat3' => 'Bantul - Yogyakarta', 'kota' => 'YOGYA', 'telp' => '0274-488338', 'search_key' => 'YOG', 'searchkey' => 'YOG'],
            ['id' => 23, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Engku Putri, Komplek Kawasan Executive Industrial Park', 'alamat2' => 'Blok C1 No. 3A, Batam Centre', 'alamat3' => 'Batam-Kep Riau', 'kota' => 'BATAM', 'telp' => '0778-7482217', 'search_key' => 'BTM', 'searchkey' => 'BTM'],
            ['id' => 24, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. Gabus No. 11', 'alamat2' => 'Kel. Lampriet/Bandar Baru Kec. Kuta Alam', 'alamat3' => 'Banda Aceh  2 3 1 2 6', 'kota' => 'ACEH', 'telp' => '0651-21202', 'search_key' => 'NAD', 'searchkey' => 'NAD'],
            ['id' => 25, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl.Raya Pemda Kedunghalang No.57.A', 'alamat2' => 'Kel.Kedunghalang - Kec.Kedunghalang', 'alamat3' => 'Bogor - 16710', 'kota' => 'BOGOR', 'telp' => '0251-8656888', 'search_key' => 'BGR', 'searchkey' => 'BGR'],
            ['id' => 26, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. ORANG KAYO PINGAI No. 40', 'alamat2' => 'Kel. Talang Banjar', 'alamat3' => 'Jambi', 'kota' => 'JAMBI', 'telp' => '0741-7554089', 'search_key' => 'JBI', 'searchkey' => 'JBI'],
            ['id' => 27, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Pulo Kambing II/26', 'alamat2' => 'Kawasan Industri Pulogadung', 'alamat3' => 'Jakarta Timur - 13930', 'kota' => 'JAKARTA PUSAT', 'telp' => '021-4615906', 'search_key' => 'JKT-00', 'searchkey' => 'JKT-00'],
            ['id' => 28, 'nama' => 'Unit Transfusi Darah PMI', 'alamat1' => 'Cab. Indramayu', 'alamat2' => 'Jl. Yos Sudarso 76/C', 'alamat3' => 'Indramayu', 'kota' => 'INDRAMAYU', 'telp' => null, 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 29, 'nama' => 'Unit Transfusi Darah PMI', 'alamat1' => 'Cab. Majalengka', 'alamat2' => 'Jl. Ibu Tien Soeharto No.331', 'alamat3' => 'Majalengka', 'kota' => 'MAJALENGKA', 'telp' => null, 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 30, 'nama' => 'Unit Transfusi Darah PMI', 'alamat1' => 'Cab. Purwakarta', 'alamat2' => 'Jl. Veteran No. 39', 'alamat3' => 'Purwakarta', 'kota' => 'PURWAKARTA', 'telp' => null, 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 31, 'nama' => 'Unit Transfusi Darah PMI', 'alamat1' => 'Cab. Subang', 'alamat2' => 'Jl. Aipda KS Tubun No.6', 'alamat3' => 'Subang', 'kota' => 'SUBANG', 'telp' => null, 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 32, 'nama' => 'Unit Transfusi Darah PMI', 'alamat1' => 'Cab. Sukabumi', 'alamat2' => 'Jl. Kenari No. 2', 'alamat3' => 'Sukabumi', 'kota' => 'SUKABUMI', 'telp' => null, 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 33, 'nama' => 'Unit Transfusi Darah PMI', 'alamat1' => 'Cab. Tangerang', 'alamat2' => 'Jl. Mayjen Sutojo No.1', 'alamat3' => 'Tangerang', 'kota' => 'TANGERANG', 'telp' => null, 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 34, 'nama' => 'PT. Pertiwi Agung (Eksport)', 'alamat1' => 'Jl. DDN Sukadanau', 'alamat2' => 'Cikarang Barat', 'alamat3' => 'Bekasi 17843', 'kota' => 'BEKASI', 'telp' => '021-88333138', 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 35, 'nama' => 'PT. Mensa Binasukses', 'alamat1' => 'Jl. Masuk Hotel Delima Komp. Pergudangan Boulevard', 'alamat2' => 'Kelapa Dua Entrop', 'alamat3' => 'Jayapura - 92222', 'kota' => 'JAYAPURA', 'telp' => '0967-550870', 'search_key' => 'JAP', 'searchkey' => 'JAP'],
            ['id' => 36, 'nama' => 'PT. Perusahaan Perdagangan Indonesia', 'alamat1' => 'Jl. Malaka No 7 - 9 Roa Malaka, Tambora', 'alamat2' => 'Jakarta Barat 11230', 'alamat3' => null, 'kota' => 'JAKARTA', 'telp' => null, 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 37, 'nama' => 'PT. Pertiwi Agung', 'alamat1' => 'Komplek DDN', 'alamat2' => 'Suka Danau', 'alamat3' => 'Cibitung - Bekasi 17520', 'kota' => 'BEKASI', 'telp' => '021-88333138', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 38, 'nama' => 'PT. Abbott Indonesia', 'alamat1' => 'D/A PT. Mensa Binasukses', 'alamat2' => 'Jl. Pulo Kambing II/26, Kawasan Industri Pulo Gadung', 'alamat3' => 'JAKARTA', 'kota' => 'JAKARTA', 'telp' => null, 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 39, 'nama' => 'PT. Mensa Bina Sukses', 'alamat1' => 'Jl. KH. Shiddiq No. 110', 'alamat2' => 'Jember Kidul Kaliwates', 'alamat3' => 'JEMBER', 'kota' => 'JEMBER', 'telp' => '0331-482332', 'search_key' => 'JMR', 'searchkey' => 'JMR'],
            ['id' => 40, 'nama' => 'PT. Mensa Binasukses', 'alamat1' => 'Jl. Suparjo Rustam RT 01 RW 007', 'alamat2' => 'Kel. Sokaraja Tengah', 'alamat3' => 'Kec. Sokaraja Kab. Banyumas', 'kota' => 'PURWOKERTO', 'telp' => '0281-6512738', 'search_key' => 'PWT', 'searchkey' => 'PWT'],
            ['id' => 41, 'nama' => 'PT. Mensa Binasukses', 'alamat1' => 'Jl. Pulo Kambing II/26', 'alamat2' => 'Kawasan Industri Pulogadung', 'alamat3' => 'Jakarta Timur - 13930', 'kota' => 'JAKARTA-9', 'telp' => null, 'search_key' => 'JAK', 'searchkey' => 'JAK'],
            ['id' => 42, 'nama' => 'PT. Mensa Binasukses', 'alamat1' => 'Jl. Siliwangi No.31 RT04 RW03', 'alamat2' => 'Kec. Bekasi Timur Kel. Sepanjang Jaya', 'alamat3' => 'Kotamadya Bekasi', 'kota' => 'BEKASI', 'telp' => '021-82416543', 'search_key' => 'BKS', 'searchkey' => 'BKS'],
            ['id' => 43, 'nama' => 'RS. PGI. CIKINI', 'alamat1' => 'JL. RADEN SALEH NO. 40', 'alamat2' => 'JAKARTA PUSAT', 'alamat3' => 'UP. GUDANG FARMASI RS PGI CIKINI', 'kota' => 'JAKARTA PUSAT', 'telp' => '021-38997777', 'search_key' => 'III', 'searchkey' => 'III'],
            ['id' => 44, 'nama' => 'PT. PROMEDRAHARDJO FARMASI INDUSTRI', 'alamat1' => 'JL. Raya Siliwangi RT 013 RW 006', 'alamat2' => 'KAB. SUKABUMI', 'alamat3' => 'JAWA BARAT', 'kota' => 'SUKABUMI', 'telp' => null, 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 45, 'nama' => 'PT. PYRIDAM FARMA Tbk.', 'alamat1' => 'Ruko Villa Kebon Jeruk', 'alamat2' => 'Jl. Raya Kebon Jeruk Blok F3', 'alamat3' => 'Jakarta Barat 11530', 'kota' => 'Jakarta', 'telp' => '0263-580844', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 46, 'nama' => 'PT. GRACIA PHARMINDO', 'alamat1' => 'Kawasan Industri Dwipapuri Blok M-30', 'alamat2' => 'Jl. Raya Rancaekek KM 24.5', 'alamat3' => 'Bandung 45364', 'kota' => 'Bandung', 'telp' => '022-4222208', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 47, 'nama' => 'ROYAL RUBY CO. LTD', 'alamat1' => 'NO. 30, ROOM NO. (101)', 'alamat2' => 'SAGAWAR STREET, DAGON T/S', 'alamat3' => 'YANGON', 'kota' => 'MYANMAR', 'telp' => null, 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 48, 'nama' => 'MONFA TRADE CO., LTD', 'alamat1' => 'BUILDING 46, JULCHIN STREET', 'alamat2' => 'CHINGELTEI - 3', 'alamat3' => 'KHOROO - ULAANBAATAR', 'kota' => 'MONGOLIA', 'telp' => null, 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 49, 'nama' => 'HANG LUNG TRADING (H. K) CO.', 'alamat1' => 'FLAT 19, 6/F, GOOD HARVEST, INDUSTRY BUILDING', 'alamat2' => '9 TSUN WAN ROAD, TMTL 232', 'alamat3' => 'TUEN MUN, N. T.,', 'kota' => 'HONG KONG', 'telp' => null, 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 50, 'nama' => 'SOFTLOGIC PHARMACEUTICALS (PVT) LTD', 'alamat1' => '14, DE FONSEKA PLACE', 'alamat2' => 'COLOMBO 05', 'alamat3' => null, 'kota' => 'SRILANKA', 'telp' => null, 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 51, 'nama' => 'WVR IMPORT EXPORT Co., Ltd', 'alamat1' => '#108AE1., Street 371', 'alamat2' => 'SANGKAT Ou baekkam Khan Sen Sok', 'alamat3' => 'PHNOM PENH, CAMBODIA', 'kota' => 'PHNOM PENH, CAMBODIA', 'telp' => null, 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 52, 'nama' => 'PT. DANKOS FARMA', 'alamat1' => 'Jln. Rawa Gatel III S, Kav. 35 - 40', 'alamat2' => 'KAWASAN INDUSTRI PULO GADUNG', 'alamat3' => 'JAKARTA TIMUR - 13930', 'kota' => 'JAKARTA', 'telp' => '021-4600158', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 53, 'nama' => 'K-SERI PHARMA SDN BHD', 'alamat1' => 'Unit 11, 1st floor, Jaya Setia Square', 'alamat2' => 'Simpang 13, Jalan Berakas BB2713', 'alamat3' => 'Bandar Seri Begawan', 'kota' => 'BRUNEI DARUSSALAM', 'telp' => '-', 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 54, 'nama' => 'PT. ERLIMPEX', 'alamat1' => 'Jl. DR. Setiabudi No. 130', 'alamat2' => 'Srondol Kulon', 'alamat3' => null, 'kota' => 'SEMARANG', 'telp' => '024-7472323', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 55, 'nama' => 'ZYFAS MEDICAL Co.', 'alamat1' => '102E Pasir Panjang Road #02-10/11', 'alamat2' => 'Citilink Warehouse Complex', 'alamat3' => 'SINGAPORE', 'kota' => 'SINGAPORE', 'telp' => '-', 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 56, 'nama' => 'APOTHECA MARKETING PTE. LTD', 'alamat1' => '63 Hillview Avenue # 09-12', 'alamat2' => 'Lam Soon Industrial Building', 'alamat3' => 'SINGAPORE 669569', 'kota' => 'SINGAPORE', 'telp' => '-', 'search_key' => 'EXP', 'searchkey' => 'EXP'],
            ['id' => 57, 'nama' => 'PT. MEPROFARM', 'alamat1' => 'JL. Soekarno-Hatta no. 789', 'alamat2' => 'BANDUNG', 'alamat3' => null, 'kota' => 'BANDUNG', 'telp' => '022-7805588', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 58, 'nama' => 'PT. HEXPHARM JAYA LABORATORIES', 'alamat1' => 'Jl. Angsana Raya Blok A3 No. 1', 'alamat2' => 'Delta silicon, Kawasan Industri Lippo Cikarang', 'alamat3' => 'BEKASI 17530', 'kota' => 'BEKASI', 'telp' => '021-89902011', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 59, 'nama' => 'PT. SOLAS LANGGENG SEJAHTERA', 'alamat1' => 'JL. Industri Cimareme I / 18', 'alamat2' => 'PADALARANG', 'alamat3' => 'BANDUNG', 'kota' => 'BANDUNG', 'telp' => '022-6868833', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 60, 'nama' => 'PT. ULTRA SAKTI', 'alamat1' => 'Jl. Pulo Kambing 1 No.26-27', 'alamat2' => 'Kawasan Industri Pulogadung', 'alamat3' => 'Jakarta Timur 13930', 'kota' => 'Jakarta Timur', 'telp' => '021-4600639', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 61, 'nama' => 'PT. METISKA FARMA', 'alamat1' => 'JL. KEBAYORAN LAMA NO. 557', 'alamat2' => 'GROGOL SELATAN-KEBAYORAN LAMA', 'alamat3' => 'JAKARTA-12220', 'kota' => 'JAKARTA', 'telp' => '021-7202351', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 62, 'nama' => 'PT. CORONET CROWN', 'alamat1' => 'JL. RAYA TAMAN KM.15', 'alamat2' => 'SURABAYA', 'alamat3' => 'TAMAN TAMAN', 'kota' => 'SIDOARJO, JAWA TIMUR', 'telp' => '031-7882473', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 63, 'nama' => 'KONIMEX', 'alamat1' => 'Desa Sanggrahan', 'alamat2' => 'Grogol', 'alamat3' => 'Sukoharjo 57552', 'kota' => 'Jawa Tengah', 'telp' => '0271-716246', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 64, 'nama' => 'PT NULAB PHARMACEUTICAL INDONESIA', 'alamat1' => 'Jl. Manis Raya', 'alamat2' => 'Gandasari - Jati Uwung', 'alamat3' => 'Tangerang 15137', 'kota' => 'Jawa Barat', 'telp' => '021-5914043', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 65, 'nama' => 'PT GUARDIAN PHARMATAMA', 'alamat1' => 'Jl. Pahlawan No. 25', 'alamat2' => 'Desa Karang Asem, Citeureup', 'alamat3' => 'Kab. Bogor', 'kota' => 'Jawa Barat', 'telp' => '021-87902588', 'search_key' => 'MAK', 'searchkey' => 'MAK'],
            ['id' => 66, 'nama' => 'SYMBIOTIC CO., LTD', 'alamat1' => 'No 33 HLAING YADANAR MON HOUSING 3rd STREET,', 'alamat2' => 'WARD No 12 HLAING TOWNSHIP,', 'alamat3' => 'YANGON REGION, 11051, MM', 'kota' => 'MYANMAR', 'telp' => null, 'search_key' => '10574', 'searchkey' => '10574'],
            ['id' => 67, 'nama' => 'VITA TRADE PVT.LTD', 'alamat1' => 'H Poonam 5B, Meheli Goalhi', 'alamat2' => 'K.Male, Maldives. Reg:C14402024', 'alamat3' => 'TIN: 1165824GST501', 'kota' => null, 'telp' => null, 'search_key' => 'EXP', 'searchkey' => 'EXP'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor');
    }
};
