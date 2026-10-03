<?php
namespace Database\Seeders;

use App\Models\Scholar;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Demo/mock data for the ISAMS list features (scholars + students).
 * Idempotent: each mock block is only inserted when it is not present yet,
 * so re-running the seeder never duplicates rows.
 */
class MockDataSeeder extends Seeder {
    public function run(): void {
        $this->seedMockScholars();
        $this->seedMockStudents();
    }

    private function seedMockScholars(): void {
        if (Scholar::where('source','External Import')->exists()) {
            $this->command?->line('Scholars: mock external list already present, skipped.');
            return;
        }

        $first = ['Maria Jose','Juan Miguel','Angelica','Joshua','Catherine','Mark Anthony','Jhon Lloyd','Krizza','Dennis','Shaira','Rico','Lovely','Ferdinand','Gracia','Kevin','Aileen','Rolando','Divina','Christian','Maricel','Elmer','Jasmine','Rogelio','Blessie','Arnel','Christine','Gilbert','Rowena','Alfredo','Melinda','Jayson','Karen','Vicente','Lorna','Reneboy','Analyn','Efren','Gemmalyn','Nestor','Precious','Jerome','Hazel','Danilo','Michelle','Ricardo','Jamaica','Armando','Cherry Mae','Bryan','Nenita'];
        $last  = ['Dela Cruz','Bautista','Villanueva','Mendoza','Santos','Reyes','Garcia','Lim','Aquino','Alonzo','Castillo','Fernandez','Salvador','Navarro','Tolentino','Mercado','Ramos','Pascual','Domingo','Cabrera','Ocampo','Velasco','Gutierrez','Sarmiento','Padilla','Soriano','Aguilar','Del Rosario','Torres','Manalo','Bagtas','Coronado','Enriquez','Fuentes','Galvez','Herrera','Ibanez','Jimenez','Lumbo','Marquez','Nepomuceno','Panganiban','Quintos','Robledo','Sicat','Tuazon','Umali','Vergara','Ybanez','Zamora'];
        $middle = ['A.','B.','C.','D.','E.','F.','G.','H.','J.','K.','L.','M.','N.','P.','R.','S.','T.','V.','W.','Y.'];
        $courses = ['BS Information Technology','BS Education','BS Nursing','BS Business Administration','BS Criminology','BS Accountancy','BS Elementary Education','BS Hospitality Management','BS Computer Science','BS Psychology'];
        $years = ['1st Year','2nd Year','3rd Year','4th Year'];
        $scholarships = [
            'CHED Merit Scholarship','DOST-SEI Undergraduate Scholarship','UniFAST Tertiary Education Subsidy',
            'PVAO Educational Benefits','SM Foundation College Scholarship','Metrobank Foundation Scholarship',
            'Ayala Foundation Scholarship','JG Summit Scholarship','DSWD Educational Assistance','GSIS Scholarship',
        ];
        $gwabases = [1.25, 1.45, 1.60, 1.75, 1.85];

        for ($i = 0; $i < 50; $i++) {
            $gwa = round($gwabases[$i % count($gwabases)] + (($i * 7) % 10) / 100, 2);
            Scholar::create([
                'student_number'   => 'scc2025-'.str_pad(60001 + $i, 6, '0', STR_PAD_LEFT),
                'first_name'       => $first[$i],
                'middle_name'      => $middle[($i * 3) % count($middle)],
                'last_name'        => $last[$i],
                'course'           => $courses[$i % count($courses)],
                'year_level'       => $years[$i % count($years)],
                'scholarship_name' => $scholarships[$i % count($scholarships)],
                'scholarship_type' => 'External',
                'status'           => 'Active',
                'current_gwa'      => min($gwa, 2.0),
                'enrollment_status'=> 'Enrolled',
                'requirements_met' => true,
                'graduation_status'=> 'On Track',
                'remarks'          => null,
                'source'           => 'External Import',
            ]);
        }
        $this->command?->line('Scholars: 50 mock external scholars seeded.');
    }

    private function seedMockStudents(): void {
        if (Student::where('student_id','like','2026-099%')->exists()) {
            $this->command?->line('Students: mock block already present, skipped.');
            return;
        }

        $first = ['Althea','Bonifacio','Camille','Dante','Erika','Francis','Gina','Homer','Isabel','Jonas','Katrina','Lorenzo','Mirasol','Nathaniel','Orlene','Paolo','Quirina','Rafael','Sandra','Teodoro','Ursula','Victor','Wenefreda','Xander','Yolanda','Zeus','Aurora','Bianca','Carlo','Divine','Elena','Felix','Gemma','Hernan','Irene','Jasper','Karla','Leandro','Marites','Nestor','Ophelia','PJ','Rebecca','Samuel','Trisha','Ulysses','Vanessa','Waldo','Xenia','Yuri'];
        $last  = ['Abad','Bautista','Cabral','Dizon','Esteban','Fabro','Galang','Hilario','Ibañez','Jacla','Kalaw','Lumibao','Manabat','Navarro','Ocampo','Pangilinan','Quiambao','Roxas','Santiago','Tiglao','Urbano','Villareal','Wagan','Ynzon','Zapata','Alcantara','Buenaventura','Cordero','Dela Peña','Escobar','Fuentes','Gonzales','Hernandez','Ignacio','Jimenez','Kho','Lizardo','Magsaysay','Nuñez','Ordoñez','Panganiban','Quintana','Ramirez','Sarmiento','Tuquero','Ureta','Valdez','Ybañez','Zabala','Andrada'];
        $middle = ['Abella','Bacani','Cruz','Danganan','Espino','Fajardo','Gatchalian','Hipolito','Ilagan','Javier'];
        $courses = ['BS Information Technology','BS Education','BS Nursing','BS Business Administration','BS Criminology','BS Accountancy','BS Elementary Education','BS Hospitality Management','BS Computer Science','BS Psychology'];
        $years = ['1st Year','2nd Year','3rd Year','4th Year'];
        $sexes = ['Female','Male'];
        $brackets = ['below_200','200_400','above_400'];
        $gwabases = [1.30, 1.55, 1.75, 2.00, 2.25];

        for ($i = 0; $i < 50; $i++) {
            Student::create([
                'first_name'      => $first[$i],
                'middle_name'     => $middle[$i % count($middle)],
                'last_name'       => $last[$i],
                'student_id'      => '2026-099'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'sex'             => $sexes[$i % 2],
                'course'          => $courses[$i % count($courses)],
                'year_level'      => $years[$i % count($years)],
                'academic_year'   => '2026-2027',
                'semester'        => '1st',
                'enrollment_type' => $i % 5 === 0 ? 'Irregular' : 'Regular',
                'income_bracket'  => $brackets[$i % 3],
                'gwa'             => round($gwabases[$i % 5] + (($i * 3) % 10) / 100, 2),
                'status'          => 'Active',
            ]);
        }
        $this->command?->line('Students: 50 mock students seeded (student_id 2026-099001 … 2026-099050).');
    }
}
