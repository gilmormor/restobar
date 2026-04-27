<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColombiaGeoSeeder extends Seeder
{
    public function run(): void
    {
        $paisId = DB::table('paises')->where('codigo_iso2', 'CO')->value('id');

        // ── 1. Departamentos (regiones) ───────────────────────────────────────
        $departamentos = [
            ['nombre' => 'Amazonas',                  'ordinal' => 'AMZ', 'orden' => 1],
            ['nombre' => 'Antioquia',                 'ordinal' => 'ANT', 'orden' => 2],
            ['nombre' => 'Arauca',                    'ordinal' => 'ARA', 'orden' => 3],
            ['nombre' => 'Atlántico',                 'ordinal' => 'ATL', 'orden' => 4],
            ['nombre' => 'Bolívar',                   'ordinal' => 'BOL', 'orden' => 5],
            ['nombre' => 'Boyacá',                    'ordinal' => 'BOY', 'orden' => 6],
            ['nombre' => 'Caldas',                    'ordinal' => 'CAL', 'orden' => 7],
            ['nombre' => 'Caquetá',                   'ordinal' => 'CAQ', 'orden' => 8],
            ['nombre' => 'Casanare',                  'ordinal' => 'CAS', 'orden' => 9],
            ['nombre' => 'Cauca',                     'ordinal' => 'CAU', 'orden' => 10],
            ['nombre' => 'Cesar',                     'ordinal' => 'CES', 'orden' => 11],
            ['nombre' => 'Chocó',                     'ordinal' => 'CHO', 'orden' => 12],
            ['nombre' => 'Córdoba',                   'ordinal' => 'COR', 'orden' => 13],
            ['nombre' => 'Cundinamarca',              'ordinal' => 'CUN', 'orden' => 14],
            ['nombre' => 'Guainía',                   'ordinal' => 'GUA', 'orden' => 15],
            ['nombre' => 'Guaviare',                  'ordinal' => 'GUV', 'orden' => 16],
            ['nombre' => 'Huila',                     'ordinal' => 'HUI', 'orden' => 17],
            ['nombre' => 'La Guajira',                'ordinal' => 'LAG', 'orden' => 18],
            ['nombre' => 'Magdalena',                 'ordinal' => 'MAG', 'orden' => 19],
            ['nombre' => 'Meta',                      'ordinal' => 'MET', 'orden' => 20],
            ['nombre' => 'Nariño',                    'ordinal' => 'NAR', 'orden' => 21],
            ['nombre' => 'Norte de Santander',        'ordinal' => 'NDS', 'orden' => 22],
            ['nombre' => 'Putumayo',                  'ordinal' => 'PUT', 'orden' => 23],
            ['nombre' => 'Quindío',                   'ordinal' => 'QUI', 'orden' => 24],
            ['nombre' => 'Risaralda',                 'ordinal' => 'RIS', 'orden' => 25],
            ['nombre' => 'San Andrés y Providencia',  'ordinal' => 'SAP', 'orden' => 26],
            ['nombre' => 'Santander',                 'ordinal' => 'SAN', 'orden' => 27],
            ['nombre' => 'Sucre',                     'ordinal' => 'SUC', 'orden' => 28],
            ['nombre' => 'Tolima',                    'ordinal' => 'TOL', 'orden' => 29],
            ['nombre' => 'Valle del Cauca',           'ordinal' => 'VAC', 'orden' => 30],
            ['nombre' => 'Vaupés',                    'ordinal' => 'VAU', 'orden' => 31],
            ['nombre' => 'Vichada',                   'ordinal' => 'VIC', 'orden' => 32],
            ['nombre' => 'Bogotá D.C.',               'ordinal' => 'BOG', 'orden' => 33],
        ];

        $deptIds = [];
        foreach ($departamentos as $d) {
            $id = DB::table('regiones')->insertGetId([
                'pais_id'    => $paisId,
                'nombre'     => $d['nombre'],
                'ordinal'    => $d['ordinal'],
                'orden'      => $d['orden'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $deptIds[$d['ordinal']] = $id;
        }

        // ── 2. Municipios (provincias) ────────────────────────────────────────
        // Formato: ['NombreMunicipio', 'ORDINAL_DEPTO']
        $municipios = [
            // AMAZONAS
            ['Leticia','AMZ'],['Puerto Nariño','AMZ'],
            // ANTIOQUIA
            ['Medellín','ANT'],['Abejorral','ANT'],['Abriaquí','ANT'],['Alejandría','ANT'],
            ['Amagá','ANT'],['Amalfi','ANT'],['Andes','ANT'],['Angelópolis','ANT'],
            ['Angostura','ANT'],['Anorí','ANT'],['Anzá','ANT'],['Apartadó','ANT'],
            ['Arboletes','ANT'],['Argelia','ANT'],['Armenia','ANT'],['Barbosa','ANT'],
            ['Bello','ANT'],['Betania','ANT'],['Betulia','ANT'],['Briceño','ANT'],
            ['Buriticá','ANT'],['Cáceres','ANT'],['Caicedo','ANT'],['Caldas','ANT'],
            ['Campamento','ANT'],['Cañasgordas','ANT'],['Caracolí','ANT'],['Caramanta','ANT'],
            ['Carepa','ANT'],['El Carmen de Viboral','ANT'],['Carolina','ANT'],['Caucasia','ANT'],
            ['Chigorodó','ANT'],['Cisneros','ANT'],['Cocorná','ANT'],['Concepción','ANT'],
            ['Concordia','ANT'],['Copacabana','ANT'],['Dabeiba','ANT'],['Don Matías','ANT'],
            ['Ebéjico','ANT'],['El Bagre','ANT'],['Entrerríos','ANT'],['Envigado','ANT'],
            ['Fredonia','ANT'],['Frontino','ANT'],['Giraldo','ANT'],['Girardota','ANT'],
            ['Gómez Plata','ANT'],['Granada','ANT'],['Guadalupe','ANT'],['Guarne','ANT'],
            ['Guatapé','ANT'],['Heliconia','ANT'],['Hispania','ANT'],['Itagüí','ANT'],
            ['Ituango','ANT'],['Jardín','ANT'],['Jericó','ANT'],['La Ceja','ANT'],
            ['La Estrella','ANT'],['La Pintada','ANT'],['La Unión','ANT'],['Liborina','ANT'],
            ['Maceo','ANT'],['Marinilla','ANT'],['Montebello','ANT'],['Murindó','ANT'],
            ['Mutatá','ANT'],['Nariño','ANT'],['Nechí','ANT'],['Necoclí','ANT'],
            ['Olaya','ANT'],['Peñol','ANT'],['Peque','ANT'],['Pueblorrico','ANT'],
            ['Puerto Berrío','ANT'],['Puerto Nare','ANT'],['Puerto Triunfo','ANT'],
            ['Remedios','ANT'],['Retiro','ANT'],['Rionegro','ANT'],['Sabanalarga','ANT'],
            ['Sabaneta','ANT'],['Salgar','ANT'],['San Andrés de Cuerquía','ANT'],
            ['San Carlos','ANT'],['San Francisco','ANT'],['San Jerónimo','ANT'],
            ['San José de la Montaña','ANT'],['San Juan de Urabá','ANT'],['San Luis','ANT'],
            ['San Pedro de los Milagros','ANT'],['San Pedro de Urabá','ANT'],
            ['San Rafael','ANT'],['San Roque','ANT'],['San Vicente Ferrer','ANT'],
            ['Santa Bárbara','ANT'],['Santa Rosa de Osos','ANT'],['Santo Domingo','ANT'],
            ['El Santuario','ANT'],['Segovia','ANT'],['Sonsón','ANT'],['Sopetrán','ANT'],
            ['Támesis','ANT'],['Tarazá','ANT'],['Tarso','ANT'],['Titiribí','ANT'],
            ['Toledo','ANT'],['Turbo','ANT'],['Uramita','ANT'],['Urrao','ANT'],
            ['Valparaíso','ANT'],['Vegachí','ANT'],['Venecia','ANT'],['Vigía del Fuerte','ANT'],
            ['Yalí','ANT'],['Yarumal','ANT'],['Yolombó','ANT'],['Yondó','ANT'],
            ['Zaragoza','ANT'],
            // ARAUCA
            ['Arauca','ARA'],['Arauquita','ARA'],['Cravo Norte','ARA'],['Fortul','ARA'],
            ['Puerto Rondón','ARA'],['Saravena','ARA'],['Tame','ARA'],
            // ATLÁNTICO
            ['Barranquilla','ATL'],['Baranoa','ATL'],['Campo de la Cruz','ATL'],
            ['Candelaria','ATL'],['Galapa','ATL'],['Juan de Acosta','ATL'],['Luruaco','ATL'],
            ['Malambo','ATL'],['Manatí','ATL'],['Palmar de Varela','ATL'],['Piojó','ATL'],
            ['Polonuevo','ATL'],['Ponedera','ATL'],['Puerto Colombia','ATL'],
            ['Repelón','ATL'],['Sabanagrande','ATL'],['Sabanalarga','ATL'],
            ['Santa Lucía','ATL'],['Santo Tomás','ATL'],['Soledad','ATL'],
            ['Suán','ATL'],['Tubará','ATL'],['Usiacurí','ATL'],
            // BOLÍVAR
            ['Cartagena','BOL'],['Achí','BOL'],['Altos del Rosario','BOL'],['Arenal','BOL'],
            ['Arjona','BOL'],['Arroyohondo','BOL'],['Barranco de Loba','BOL'],
            ['Calamar','BOL'],['Cantagallo','BOL'],['Cicuco','BOL'],['Clemencia','BOL'],
            ['El Carmen de Bolívar','BOL'],['El Guamo','BOL'],['El Peñón','BOL'],
            ['Hatillo de Loba','BOL'],['Magangué','BOL'],['Mahates','BOL'],
            ['Margarita','BOL'],['María la Baja','BOL'],['Mompós','BOL'],
            ['Montecristo','BOL'],['Morales','BOL'],['Norosí','BOL'],['Pinillos','BOL'],
            ['Regidor','BOL'],['Río Viejo','BOL'],['San Cristóbal','BOL'],
            ['San Estanislao','BOL'],['San Fernando','BOL'],['San Jacinto','BOL'],
            ['San Jacinto del Cauca','BOL'],['San Juan Nepomuceno','BOL'],
            ['San Martín de Loba','BOL'],['San Pablo','BOL'],['Santa Catalina','BOL'],
            ['Santa Rosa','BOL'],['Santa Rosa del Sur','BOL'],['Simití','BOL'],
            ['Soplaviento','BOL'],['Talaigua Nuevo','BOL'],['Tiquisio','BOL'],
            ['Turbaco','BOL'],['Turbaná','BOL'],['Villanueva','BOL'],['Zambrano','BOL'],
            // BOYACÁ
            ['Tunja','BOY'],['Almeida','BOY'],['Aquitania','BOY'],['Arcabuco','BOY'],
            ['Belén','BOY'],['Berbeo','BOY'],['Betéitiva','BOY'],['Boavita','BOY'],
            ['Boyacá','BOY'],['Briceño','BOY'],['Buenavista','BOY'],['Busbanzá','BOY'],
            ['Caldas','BOY'],['Campohermoso','BOY'],['Cerinza','BOY'],['Chinavita','BOY'],
            ['Chiquinquirá','BOY'],['Chíquiza','BOY'],['Chiscas','BOY'],['Chita','BOY'],
            ['Chitaraque','BOY'],['Chivatá','BOY'],['Chivor','BOY'],['Ciénega','BOY'],
            ['Cómbita','BOY'],['Coper','BOY'],['Corrales','BOY'],['Covarachía','BOY'],
            ['Cubará','BOY'],['Cucaita','BOY'],['Cuítiva','BOY'],['Duitama','BOY'],
            ['El Cocuy','BOY'],['El Espino','BOY'],['Firavitoba','BOY'],['Floresta','BOY'],
            ['Gachantivá','BOY'],['Gámeza','BOY'],['Garagoa','BOY'],['Guacamayas','BOY'],
            ['Guateque','BOY'],['Guayatá','BOY'],['Güicán de la Sierra','BOY'],
            ['Iza','BOY'],['Jenesano','BOY'],['Jericó','BOY'],['La Capilla','BOY'],
            ['La Uvita','BOY'],['La Victoria','BOY'],['Labranzagrande','BOY'],
            ['Macanal','BOY'],['Maripí','BOY'],['Miraflores','BOY'],['Mongua','BOY'],
            ['Monguí','BOY'],['Moniquirá','BOY'],['Motavita','BOY'],['Muzo','BOY'],
            ['Nobsa','BOY'],['Nuevo Colón','BOY'],['Oicatá','BOY'],['Otanche','BOY'],
            ['Pachavita','BOY'],['Páez','BOY'],['Paipa','BOY'],['Pajarito','BOY'],
            ['Panqueba','BOY'],['Pauna','BOY'],['Paya','BOY'],['Paz de Río','BOY'],
            ['Pesca','BOY'],['Pisba','BOY'],['Puerto Boyacá','BOY'],['Quípama','BOY'],
            ['Ramiriquí','BOY'],['Ráquira','BOY'],['Rondón','BOY'],['Saboyá','BOY'],
            ['Sáchica','BOY'],['Samacá','BOY'],['San Eduardo','BOY'],
            ['San José de Pare','BOY'],['San Luis de Gaceno','BOY'],['San Mateo','BOY'],
            ['San Miguel de Sema','BOY'],['San Pablo de Borbur','BOY'],
            ['Santa María','BOY'],['Santa Rosa de Viterbo','BOY'],['Santa Sofía','BOY'],
            ['Santana','BOY'],['Sativanorte','BOY'],['Sativasur','BOY'],
            ['Siachoque','BOY'],['Soatá','BOY'],['Socotá','BOY'],['Socha','BOY'],
            ['Sogamoso','BOY'],['Somondoco','BOY'],['Sora','BOY'],['Soracá','BOY'],
            ['Sotaquirá','BOY'],['Susacón','BOY'],['Sutamarchán','BOY'],
            ['Sutatenza','BOY'],['Tasco','BOY'],['Tenza','BOY'],['Tibaná','BOY'],
            ['Tibasosa','BOY'],['Tinjacá','BOY'],['Tipacoque','BOY'],['Toca','BOY'],
            ['Togüí','BOY'],['Tota','BOY'],['Turmequé','BOY'],['Tuta','BOY'],
            ['Tutazá','BOY'],['Úmbita','BOY'],['Ventaquemada','BOY'],
            ['Villa de Leyva','BOY'],['Viracachá','BOY'],['Zetaquira','BOY'],
            // CALDAS
            ['Manizales','CAL'],['Aguadas','CAL'],['Anserma','CAL'],['Aranzazu','CAL'],
            ['Belalcázar','CAL'],['Chinchiná','CAL'],['Filadelfia','CAL'],
            ['La Dorada','CAL'],['La Merced','CAL'],['Manzanares','CAL'],['Marmato','CAL'],
            ['Marquetalia','CAL'],['Marulanda','CAL'],['Neira','CAL'],['Norcasia','CAL'],
            ['Pácora','CAL'],['Palestina','CAL'],['Pensilvania','CAL'],['Riosucio','CAL'],
            ['Risaralda','CAL'],['Salamina','CAL'],['Samaná','CAL'],['San José','CAL'],
            ['Supía','CAL'],['Victoria','CAL'],['Villamaría','CAL'],['Viterbo','CAL'],
            // CAQUETÁ
            ['Florencia','CAQ'],['Albania','CAQ'],['Belén de los Andaquíes','CAQ'],
            ['Cartagena del Chairá','CAQ'],['Curillo','CAQ'],['El Doncello','CAQ'],
            ['El Paujil','CAQ'],['La Montañita','CAQ'],['Milán','CAQ'],['Morelia','CAQ'],
            ['Puerto Rico','CAQ'],['San José del Fragua','CAQ'],
            ['San Vicente del Caguán','CAQ'],['Solano','CAQ'],['Solita','CAQ'],
            ['Valparaíso','CAQ'],
            // CASANARE
            ['Yopal','CAS'],['Aguazul','CAS'],['Chámeza','CAS'],['Hato Corozal','CAS'],
            ['La Salina','CAS'],['Maní','CAS'],['Monterrey','CAS'],['Nunchía','CAS'],
            ['Orocué','CAS'],['Paz de Ariporo','CAS'],['Pore','CAS'],['Recetor','CAS'],
            ['Sabanalarga','CAS'],['Sácama','CAS'],['San Luis de Palenque','CAS'],
            ['Támara','CAS'],['Tauramena','CAS'],['Trinidad','CAS'],['Villanueva','CAS'],
            // CAUCA
            ['Popayán','CAU'],['Almaguer','CAU'],['Argelia','CAU'],['Balboa','CAU'],
            ['Bolívar','CAU'],['Buenos Aires','CAU'],['Cajibío','CAU'],['Caldono','CAU'],
            ['Caloto','CAU'],['Corinto','CAU'],['El Tambo','CAU'],['Florencia','CAU'],
            ['Guachené','CAU'],['Guapi','CAU'],['Inzá','CAU'],['Jambaló','CAU'],
            ['La Sierra','CAU'],['La Vega','CAU'],['López de Micay','CAU'],
            ['Mercaderes','CAU'],['Miranda','CAU'],['Morales','CAU'],['Padilla','CAU'],
            ['Páez','CAU'],['Patía','CAU'],['Piamonte','CAU'],['Piendamó','CAU'],
            ['Puerto Tejada','CAU'],['Puracé','CAU'],['Rosas','CAU'],
            ['San Sebastián','CAU'],['Santander de Quilichao','CAU'],
            ['Santa Rosa','CAU'],['Silvia','CAU'],['Sotará','CAU'],['Suárez','CAU'],
            ['Sucre','CAU'],['Timbío','CAU'],['Timbiquí','CAU'],['Toribío','CAU'],
            ['Totoró','CAU'],['Villa Rica','CAU'],
            // CESAR
            ['Valledupar','CES'],['Aguachica','CES'],['Agustín Codazzi','CES'],
            ['Astrea','CES'],['Becerril','CES'],['Bosconia','CES'],['Chimichagua','CES'],
            ['Chiriguaná','CES'],['Curumaní','CES'],['El Copey','CES'],['El Paso','CES'],
            ['Gamarra','CES'],['González','CES'],['La Gloria','CES'],
            ['La Jagua de Ibirico','CES'],['La Paz','CES'],
            ['Manaure Balcón del Cesar','CES'],['Pailitas','CES'],['Pelaya','CES'],
            ['Pueblo Bello','CES'],['Río de Oro','CES'],['San Alberto','CES'],
            ['San Diego','CES'],['San Martín','CES'],['Tamalameque','CES'],
            // CHOCÓ
            ['Quibdó','CHO'],['Acandí','CHO'],['Alto Baudó','CHO'],['Atrato','CHO'],
            ['Bagadó','CHO'],['Bahía Solano','CHO'],['Bajo Baudó','CHO'],['Bojayá','CHO'],
            ['Carmen del Darién','CHO'],['Cértegui','CHO'],['Condoto','CHO'],
            ['El Carmen de Atrato','CHO'],['El Litoral del San Juan','CHO'],
            ['Istmina','CHO'],['Juradó','CHO'],['Lloró','CHO'],['Medio Atrato','CHO'],
            ['Medio Baudó','CHO'],['Medio San Juan','CHO'],['Nóvita','CHO'],
            ['Nuquí','CHO'],['Río Iro','CHO'],['Río Quito','CHO'],['Riosucio','CHO'],
            ['San José del Palmar','CHO'],['Sipí','CHO'],['Tadó','CHO'],
            ['Unguía','CHO'],['Unión Panamericana','CHO'],
            // CÓRDOBA
            ['Montería','COR'],['Ayapel','COR'],['Buenavista','COR'],['Canalete','COR'],
            ['Cereté','COR'],['Chimá','COR'],['Chinú','COR'],['Ciénaga de Oro','COR'],
            ['Cotorra','COR'],['La Apartada','COR'],['Lorica','COR'],
            ['Los Córdobas','COR'],['Momil','COR'],['Montelíbano','COR'],
            ['Moñitos','COR'],['Planeta Rica','COR'],['Pueblo Nuevo','COR'],
            ['Puerto Escondido','COR'],['Puerto Libertador','COR'],
            ['Purísima de la Concepción','COR'],['Sahagún','COR'],
            ['San Andrés de Sotavento','COR'],['San Antero','COR'],
            ['San Bernardo del Viento','COR'],['San Carlos','COR'],
            ['San José de Uré','COR'],['San Pelayo','COR'],['Tierralta','COR'],
            ['Valencia','COR'],
            // CUNDINAMARCA
            ['Agua de Dios','CUN'],['Albán','CUN'],['Anapoima','CUN'],['Anolaima','CUN'],
            ['Apulo','CUN'],['Arbeláez','CUN'],['Beltrán','CUN'],['Bituima','CUN'],
            ['Bojacá','CUN'],['Cabrera','CUN'],['Cachipay','CUN'],['Cajicá','CUN'],
            ['Caparrapí','CUN'],['Cáqueza','CUN'],['Carmen de Carupa','CUN'],
            ['Chaguaní','CUN'],['Chía','CUN'],['Chipaque','CUN'],['Choachí','CUN'],
            ['Chocontá','CUN'],['Cogua','CUN'],['Cota','CUN'],['Cucunubá','CUN'],
            ['El Colegio','CUN'],['El Peñón','CUN'],['El Rosal','CUN'],
            ['Facatativá','CUN'],['Fomeque','CUN'],['Fosca','CUN'],['Funza','CUN'],
            ['Fúquene','CUN'],['Fusagasugá','CUN'],['Gachalá','CUN'],
            ['Gachancipá','CUN'],['Gachetá','CUN'],['Gama','CUN'],['Girardot','CUN'],
            ['Granada','CUN'],['Guachetá','CUN'],['Guaduas','CUN'],['Guasca','CUN'],
            ['Guataquí','CUN'],['Guatavita','CUN'],['Guayabal de Síquima','CUN'],
            ['Guayabetal','CUN'],['Gutiérrez','CUN'],['Jerusalén','CUN'],['Junín','CUN'],
            ['La Calera','CUN'],['La Mesa','CUN'],['La Palma','CUN'],['La Peña','CUN'],
            ['La Vega','CUN'],['Lenguazaque','CUN'],['Machetá','CUN'],['Madrid','CUN'],
            ['Manta','CUN'],['Medina','CUN'],['Mosquera','CUN'],['Nariño','CUN'],
            ['Nemocón','CUN'],['Nilo','CUN'],['Nimaima','CUN'],['Nocaima','CUN'],
            ['Pandi','CUN'],['Paratebueno','CUN'],['Pasca','CUN'],
            ['Puerto Salgar','CUN'],['Pulí','CUN'],['Quebradanegra','CUN'],
            ['Quetame','CUN'],['Quipile','CUN'],['Ricaurte','CUN'],
            ['San Antonio del Tequendama','CUN'],['San Bernardo','CUN'],
            ['San Cayetano','CUN'],['San Francisco','CUN'],
            ['San Juan de Río Seco','CUN'],['Sasaima','CUN'],['Sesquilé','CUN'],
            ['Sibaté','CUN'],['Silvania','CUN'],['Simijaca','CUN'],['Soacha','CUN'],
            ['Sopó','CUN'],['Subachoque','CUN'],['Suesca','CUN'],['Supatá','CUN'],
            ['Susa','CUN'],['Sutatausa','CUN'],['Tabio','CUN'],['Tausa','CUN'],
            ['Tena','CUN'],['Tibacuy','CUN'],['Tibiritá','CUN'],['Tocaima','CUN'],
            ['Tocancipá','CUN'],['Topaipí','CUN'],['Ubalá','CUN'],['Ubaque','CUN'],
            ['Ubaté','CUN'],['Une','CUN'],['Útica','CUN'],['Vergara','CUN'],
            ['Vianí','CUN'],['Villa de San Diego de Ubaté','CUN'],['Villagómez','CUN'],
            ['Villapinzón','CUN'],['Villeta','CUN'],['Viotá','CUN'],['Yacopí','CUN'],
            ['Zipacón','CUN'],['Zipaquirá','CUN'],
            // GUAINÍA
            ['Inírida','GUA'],['Barranco Minas','GUA'],['Cacahual','GUA'],
            ['La Guadalupe','GUA'],['Morichal','GUA'],['Pana Pana','GUA'],
            ['Puerto Colombia','GUA'],['San Felipe','GUA'],
            // GUAVIARE
            ['San José del Guaviare','GUV'],['Calamar','GUV'],
            ['El Retorno','GUV'],['Miraflores','GUV'],
            // HUILA
            ['Neiva','HUI'],['Acevedo','HUI'],['Agrado','HUI'],['Aipe','HUI'],
            ['Algeciras','HUI'],['Altamira','HUI'],['Baraya','HUI'],
            ['Campoalegre','HUI'],['Colombia','HUI'],['Elías','HUI'],['Garzón','HUI'],
            ['Gigante','HUI'],['Guadalupe','HUI'],['Hobo','HUI'],['Iquira','HUI'],
            ['Isnos','HUI'],['La Argentina','HUI'],['La Plata','HUI'],['Nátaga','HUI'],
            ['Oporapa','HUI'],['Paicol','HUI'],['Palermo','HUI'],['Palestina','HUI'],
            ['Pital','HUI'],['Pitalito','HUI'],['Rivera','HUI'],['Saladoblanco','HUI'],
            ['San Agustín','HUI'],['Santa María','HUI'],['Suaza','HUI'],['Tarqui','HUI'],
            ['Tesalia','HUI'],['Tello','HUI'],['Teruel','HUI'],['Timaná','HUI'],
            ['Villavieja','HUI'],['Yaguará','HUI'],
            // LA GUAJIRA
            ['Riohacha','LAG'],['Albania','LAG'],['Barrancas','LAG'],['Dibulla','LAG'],
            ['Distracción','LAG'],['El Molino','LAG'],['Fonseca','LAG'],
            ['Hatonuevo','LAG'],['La Jagua del Pilar','LAG'],['Maicao','LAG'],
            ['Manaure','LAG'],['San Juan del Cesar','LAG'],['Uribia','LAG'],
            ['Urumita','LAG'],['Villanueva','LAG'],
            // MAGDALENA
            ['Santa Marta','MAG'],['Algarrobo','MAG'],['Aracataca','MAG'],
            ['Ariguaní','MAG'],['Cerro de San Antonio','MAG'],['Chivolo','MAG'],
            ['Ciénaga','MAG'],['Concordia','MAG'],['El Banco','MAG'],['El Piñón','MAG'],
            ['El Retén','MAG'],['Fundación','MAG'],['Guamal','MAG'],
            ['Nueva Granada','MAG'],['Pedraza','MAG'],['Pijiño del Carmen','MAG'],
            ['Pivijay','MAG'],['Plato','MAG'],['Puebloviejo','MAG'],['Remolino','MAG'],
            ['Sabanas de San Ángel','MAG'],['Salamina','MAG'],
            ['San Sebastián de Buenavista','MAG'],['San Zenón','MAG'],
            ['Santa Ana','MAG'],['Santa Bárbara de Pinto','MAG'],
            ['Sitionuevo','MAG'],['Tenerife','MAG'],['Zapayán','MAG'],
            ['Zona Bananera','MAG'],
            // META
            ['Villavicencio','MET'],['Acacías','MET'],['Barranca de Upía','MET'],
            ['Cabuyaro','MET'],['Castilla la Nueva','MET'],['Cubarral','MET'],
            ['Cumaral','MET'],['El Calvario','MET'],['El Castillo','MET'],
            ['El Dorado','MET'],['Fuente de Oro','MET'],['Granada','MET'],
            ['Guamal','MET'],['La Macarena','MET'],['La Uribe','MET'],
            ['Lejanías','MET'],['Mapiripán','MET'],['Mesetas','MET'],
            ['Puerto Concordia','MET'],['Puerto Gaitán','MET'],['Puerto Lleras','MET'],
            ['Puerto López','MET'],['Puerto Rico','MET'],['Restrepo','MET'],
            ['San Carlos de Guaroa','MET'],['San Juan de Arama','MET'],
            ['San Juanito','MET'],['San Martín','MET'],['Vistahermosa','MET'],
            // NARIÑO
            ['Pasto','NAR'],['Albán','NAR'],['Aldana','NAR'],['Ancuyá','NAR'],
            ['Arboleda','NAR'],['Barbacoas','NAR'],['Belén','NAR'],['Buesaco','NAR'],
            ['Chachagüí','NAR'],['Colón','NAR'],['Consacá','NAR'],['Contadero','NAR'],
            ['Córdoba','NAR'],['Cuaspud Carlosama','NAR'],['Cumbal','NAR'],
            ['Cumbitara','NAR'],['El Charco','NAR'],['El Peñol','NAR'],
            ['El Rosario','NAR'],['El Tablón de Gómez','NAR'],['El Tambo','NAR'],
            ['Francisco Pizarro','NAR'],['Funes','NAR'],['Guachucal','NAR'],
            ['Guaitarilla','NAR'],['Gualmatán','NAR'],['Iles','NAR'],['Imués','NAR'],
            ['Ipiales','NAR'],['La Cruz','NAR'],['La Florida','NAR'],
            ['La Llanada','NAR'],['La Tola','NAR'],['La Unión','NAR'],['Leiva','NAR'],
            ['Linares','NAR'],['Los Andes','NAR'],['Magüí Payán','NAR'],
            ['Mallama','NAR'],['Mosquera','NAR'],['Nariño','NAR'],
            ['Olaya Herrera','NAR'],['Ospina','NAR'],['Policarpa','NAR'],
            ['Potosí','NAR'],['Providencia','NAR'],['Puerres','NAR'],['Pupiales','NAR'],
            ['Ricaurte','NAR'],['Roberto Payán','NAR'],['Samaniego','NAR'],
            ['San Bernardo','NAR'],['San Lorenzo','NAR'],['San Pablo','NAR'],
            ['San Pedro de Cartago','NAR'],['Sandoná','NAR'],['Santa Bárbara','NAR'],
            ['Santacruz','NAR'],['Sapuyes','NAR'],['Taminango','NAR'],['Tangua','NAR'],
            ['Tumaco','NAR'],['Túquerres','NAR'],['Yacuanquer','NAR'],
            // NORTE DE SANTANDER
            ['Cúcuta','NDS'],['Ábrego','NDS'],['Arboledas','NDS'],['Bochalema','NDS'],
            ['Bucarasica','NDS'],['Cácota','NDS'],['Cachirá','NDS'],['Chinácota','NDS'],
            ['Chitagá','NDS'],['Convención','NDS'],['Cucutilla','NDS'],['Durania','NDS'],
            ['El Carmen','NDS'],['El Tarra','NDS'],['El Zulia','NDS'],['Gramalote','NDS'],
            ['Hacarí','NDS'],['Herrán','NDS'],['La Esperanza','NDS'],
            ['La Playa de Belén','NDS'],['Labateca','NDS'],['Los Patios','NDS'],
            ['Lourdes','NDS'],['Mutiscua','NDS'],['Ocaña','NDS'],['Pamplona','NDS'],
            ['Pamplonita','NDS'],['Puerto Santander','NDS'],['Ragonvalia','NDS'],
            ['Salazar','NDS'],['San Calixto','NDS'],['San Cayetano','NDS'],
            ['Santiago','NDS'],['Sardinata','NDS'],['Silos','NDS'],['Teorama','NDS'],
            ['Tibú','NDS'],['Toledo','NDS'],['Villa Caro','NDS'],
            ['Villa del Rosario','NDS'],
            // PUTUMAYO
            ['Mocoa','PUT'],['Colón','PUT'],['Orito','PUT'],['Puerto Asís','PUT'],
            ['Puerto Caicedo','PUT'],['Puerto Guzmán','PUT'],['Puerto Leguízamo','PUT'],
            ['San Francisco','PUT'],['San Miguel','PUT'],['Santiago','PUT'],
            ['Sibundoy','PUT'],['Valle del Guamuez','PUT'],['Villagarzón','PUT'],
            // QUINDÍO
            ['Armenia','QUI'],['Buenavista','QUI'],['Calarcá','QUI'],['Circasia','QUI'],
            ['Córdoba','QUI'],['Filandia','QUI'],['Génova','QUI'],['La Tebaida','QUI'],
            ['Montenegro','QUI'],['Pijao','QUI'],['Quimbaya','QUI'],['Salento','QUI'],
            // RISARALDA
            ['Pereira','RIS'],['Apía','RIS'],['Balboa','RIS'],['Belén de Umbría','RIS'],
            ['Dosquebradas','RIS'],['Guática','RIS'],['La Celia','RIS'],
            ['La Virginia','RIS'],['Marsella','RIS'],['Mistrató','RIS'],
            ['Pueblo Rico','RIS'],['Quinchía','RIS'],['Santa Rosa de Cabal','RIS'],
            ['Santuario','RIS'],
            // SAN ANDRÉS Y PROVIDENCIA
            ['San Andrés','SAP'],['Providencia','SAP'],
            // SANTANDER
            ['Bucaramanga','SAN'],['Aguada','SAN'],['Albania','SAN'],['Aratoca','SAN'],
            ['Barbosa','SAN'],['Barichara','SAN'],['Barrancabermeja','SAN'],
            ['Betulia','SAN'],['Bolívar','SAN'],['Cabrera','SAN'],['California','SAN'],
            ['Capitanejo','SAN'],['Carcasí','SAN'],['Cepitá','SAN'],['Cerrito','SAN'],
            ['Charalá','SAN'],['Charta','SAN'],['Chima','SAN'],['Chipatá','SAN'],
            ['Cimitarra','SAN'],['Concepción','SAN'],['Confines','SAN'],
            ['Contratación','SAN'],['Coromoro','SAN'],['Curití','SAN'],
            ['El Carmen de Chucurí','SAN'],['El Guacamayo','SAN'],['El Peñón','SAN'],
            ['El Playón','SAN'],['Encino','SAN'],['Enciso','SAN'],['Florián','SAN'],
            ['Floridablanca','SAN'],['Galán','SAN'],['Gambita','SAN'],['Girón','SAN'],
            ['Guaca','SAN'],['Guadalupe','SAN'],['Guapotá','SAN'],['Guavatá','SAN'],
            ['Güepsa','SAN'],['Hato','SAN'],['Jesús María','SAN'],['Jordán','SAN'],
            ['La Belleza','SAN'],['La Paz','SAN'],['Landázuri','SAN'],['Lebrija','SAN'],
            ['Los Santos','SAN'],['Macaravita','SAN'],['Málaga','SAN'],['Matanza','SAN'],
            ['Mogotes','SAN'],['Molagavita','SAN'],['Ocamonte','SAN'],['Oiba','SAN'],
            ['Onzaga','SAN'],['Palmar','SAN'],['Palmas del Socorro','SAN'],
            ['Páramo','SAN'],['Piedecuesta','SAN'],['Pinchote','SAN'],
            ['Puente Nacional','SAN'],['Puerto Parra','SAN'],['Puerto Wilches','SAN'],
            ['Rionegro','SAN'],['Sabana de Torres','SAN'],['San Andrés','SAN'],
            ['San Benito','SAN'],['San Gil','SAN'],['San Joaquín','SAN'],
            ['San José de Miranda','SAN'],['San Miguel','SAN'],
            ['San Vicente de Chucurí','SAN'],['Santa Bárbara','SAN'],
            ['Santa Helena del Opón','SAN'],['Simacota','SAN'],['Socorro','SAN'],
            ['Suaita','SAN'],['Sucre','SAN'],['Suratá','SAN'],['Tona','SAN'],
            ['Valle de San José','SAN'],['Vélez','SAN'],['Vetas','SAN'],
            ['Villanueva','SAN'],['Zapatoca','SAN'],
            // SUCRE
            ['Sincelejo','SUC'],['Buenavista','SUC'],['Caimito','SUC'],['Chalán','SUC'],
            ['Colosó','SUC'],['Corozal','SUC'],['Coveñas','SUC'],['El Roble','SUC'],
            ['Galeras','SUC'],['Guaranda','SUC'],['La Unión','SUC'],
            ['Los Palmitos','SUC'],['Majagual','SUC'],['Morroa','SUC'],['Ovejas','SUC'],
            ['Palmito','SUC'],['Sampués','SUC'],['San Benito Abad','SUC'],
            ['San Juan de Betulia','SUC'],['San Marcos','SUC'],['San Onofre','SUC'],
            ['San Pedro','SUC'],['Santiago de Tolú','SUC'],['Sincé','SUC'],
            ['Sucre','SUC'],['Tolú Viejo','SUC'],
            // TOLIMA
            ['Ibagué','TOL'],['Alpujarra','TOL'],['Alvarado','TOL'],['Ambalema','TOL'],
            ['Anzoátegui','TOL'],['Armero','TOL'],['Ataco','TOL'],['Cajamarca','TOL'],
            ['Carmen de Apicalá','TOL'],['Casabianca','TOL'],['Chaparral','TOL'],
            ['Coello','TOL'],['Coyaima','TOL'],['Cunday','TOL'],['Dolores','TOL'],
            ['Espinal','TOL'],['Falan','TOL'],['Flandes','TOL'],['Fresno','TOL'],
            ['Guamo','TOL'],['Herveo','TOL'],['Honda','TOL'],['Icononzo','TOL'],
            ['Lérida','TOL'],['Líbano','TOL'],['Mariquita','TOL'],['Melgar','TOL'],
            ['Murillo','TOL'],['Natagaima','TOL'],['Ortega','TOL'],['Palocabildo','TOL'],
            ['Piedras','TOL'],['Planadas','TOL'],['Prado','TOL'],['Purificación','TOL'],
            ['Rioblanco','TOL'],['Roncesvalles','TOL'],['Rovira','TOL'],['Saldaña','TOL'],
            ['San Antonio','TOL'],['San Luis','TOL'],['Santa Isabel','TOL'],
            ['Suárez','TOL'],['Valle de San Juan','TOL'],['Venadillo','TOL'],
            ['Villahermosa','TOL'],['Villarrica','TOL'],
            // VALLE DEL CAUCA
            ['Cali','VAC'],['Alcalá','VAC'],['Andalucía','VAC'],
            ['Ansermanuevo','VAC'],['Argelia','VAC'],['Bolívar','VAC'],
            ['Buenaventura','VAC'],['Buga','VAC'],['Bugalagrande','VAC'],
            ['Caicedonia','VAC'],['Calima','VAC'],['Candelaria','VAC'],['Cartago','VAC'],
            ['Dagua','VAC'],['El Águila','VAC'],['El Cairo','VAC'],['El Cerrito','VAC'],
            ['El Dovio','VAC'],['Florida','VAC'],['Ginebra','VAC'],['Guacarí','VAC'],
            ['Jamundí','VAC'],['La Cumbre','VAC'],['La Unión','VAC'],
            ['La Victoria','VAC'],['Obando','VAC'],['Palmira','VAC'],['Pradera','VAC'],
            ['Restrepo','VAC'],['Riofrío','VAC'],['Roldanillo','VAC'],['San Pedro','VAC'],
            ['Sevilla','VAC'],['Toro','VAC'],['Trujillo','VAC'],['Tuluá','VAC'],
            ['Ulloa','VAC'],['Versalles','VAC'],['Vijes','VAC'],['Yotoco','VAC'],
            ['Yumbo','VAC'],['Zarzal','VAC'],
            // VAUPÉS
            ['Mitú','VAU'],['Carurú','VAU'],['Taraira','VAU'],
            // VICHADA
            ['Puerto Carreño','VIC'],['Cumaribo','VIC'],
            ['La Primavera','VIC'],['Santa Rosalía','VIC'],
            // BOGOTÁ D.C.
            ['Bogotá D.C.','BOG'],
        ];

        // Insertar municipios en chunks y guardar IDs de los principales
        $municipioIds = []; // nombre+depto => id
        $chunks = array_chunk($municipios, 100);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $m) {
                $id = DB::table('provincias')->insertGetId([
                    'nombre'     => $m[0],
                    'region_id'  => $deptIds[$m[1]],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $municipioIds[$m[0].'|'.$m[1]] = $id;
            }
        }

        // ── 3. Comunas ────────────────────────────────────────────────────────
        // Ciudades con comunas reales
        $comunasEspeciales = [
            // BOGOTÁ D.C. — 20 localidades
            'Bogotá D.C.|BOG' => [
                'Usaquén','Chapinero','Santa Fe','San Cristóbal','Usme',
                'Tunjuelito','Bosa','Kennedy','Fontibón','Engativá','Suba',
                'Barrios Unidos','Teusaquillo','Los Mártires','Antonio Nariño',
                'Puente Aranda','La Candelaria','Rafael Uribe Uribe',
                'Ciudad Bolívar','Sumapaz',
            ],
            // MEDELLÍN — 16 comunas + 5 corregimientos
            'Medellín|ANT' => [
                'Popular','Santa Cruz','Manrique','Aranjuez','Castilla',
                'Doce de Octubre','Robledo','Villa Hermosa','Buenos Aires',
                'La Candelaria','Laureles-Estadio','La América','San Javier',
                'El Poblado','Guayabal','Belén',
                'San Sebastián de Palmitas','San Cristóbal','Altavista',
                'San Antonio de Prado','Santa Elena',
            ],
            // CALI — 22 comunas
            'Cali|VAC' => [
                'Comuna 1','Comuna 2','Comuna 3','Comuna 4','Comuna 5',
                'Comuna 6','Comuna 7','Comuna 8','Comuna 9','Comuna 10',
                'Comuna 11','Comuna 12','Comuna 13','Comuna 14','Comuna 15',
                'Comuna 16','Comuna 17','Comuna 18','Comuna 19','Comuna 20',
                'Comuna 21','Comuna 22',
            ],
            // BARRANQUILLA — 5 localidades
            'Barranquilla|ATL' => [
                'Riomar','Norte-Centro Histórico','Suroccidente',
                'Suroriente','Metropolitana',
            ],
            // CARTAGENA — 3 localidades
            'Cartagena|BOL' => [
                'Histórica y del Caribe Norte',
                'De la Virgen y Turística',
                'Industrial y de la Bahía',
            ],
            // BUCARAMANGA — 17 comunas
            'Bucaramanga|SAN' => [
                'Norte','Sur Occidente','García Rovira','Occidental',
                'Ciudadela Real de Minas','La Ciudadela','Sur','Morrorico',
                'La Concordia','Provenza','Cabecera del Llano','Río Frío',
                'Álvarez','Oriental','El Prado','San Francisco',
                'Lagos del Cacique',
            ],
            // MANIZALES — 11 comunas
            'Manizales|CAL' => [
                'Ciudadela del Norte','La Fuente','Cumanday','La Estación',
                'Atardeceres','Ecoturístico Cerro de Oro','Macarena',
                'Palogrande','Universitaria','Villahermosa','La Palma',
            ],
            // PEREIRA — 19 comunas
            'Pereira|RIS' => array_map(fn($n) => "Comuna $n", range(1, 19)),
            // IBAGUÉ — 13 comunas
            'Ibagué|TOL' => array_map(fn($n) => "Comuna $n", range(1, 13)),
            // CÚCUTA — 10 comunas
            'Cúcuta|NDS' => array_map(fn($n) => "Comuna $n", range(1, 10)),
            // PASTO — 12 comunas
            'Pasto|NAR' => array_map(fn($n) => "Comuna $n", range(1, 12)),
            // MONTERÍA — 9 comunas
            'Montería|COR' => array_map(fn($n) => "Comuna $n", range(1, 9)),
            // VILLAVICENCIO — 8 comunas
            'Villavicencio|MET' => array_map(fn($n) => "Comuna $n", range(1, 8)),
            // SANTA MARTA — 3 localidades
            'Santa Marta|MAG' => ['Norte','Sur','Histórica'],
            // NEIVA — 10 comunas
            'Neiva|HUI' => array_map(fn($n) => "Comuna $n", range(1, 10)),
            // ARMENIA — 10 comunas
            'Armenia|QUI' => array_map(fn($n) => "Comuna $n", range(1, 10)),
            // POPAYÁN — 9 comunas
            'Popayán|CAU' => array_map(fn($n) => "Comuna $n", range(1, 9)),
            // VALLEDUPAR — 10 comunas
            'Valledupar|CES' => array_map(fn($n) => "Comuna $n", range(1, 10)),
            // SOLEDAD — 5 comunas
            'Soledad|ATL' => array_map(fn($n) => "Comuna $n", range(1, 5)),
            // ITAGÜÍ — 10 comunas
            'Itagüí|ANT' => array_map(fn($n) => "Comuna $n", range(1, 10)),
            // BELLO — 11 comunas
            'Bello|ANT' => array_map(fn($n) => "Comuna $n", range(1, 11)),
        ];

        // Municipios especiales que ya tienen comunas definidas
        $municipiosConComunas = array_keys($comunasEspeciales);

        // Insertar comunas especiales
        $comunasInsert = [];
        foreach ($comunasEspeciales as $key => $comunas) {
            if (!isset($municipioIds[$key])) continue;
            $provId = $municipioIds[$key];
            foreach ($comunas as $c) {
                $comunasInsert[] = [
                    'nombre'      => $c,
                    'provincia_id'=> $provId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        // Para el resto de municipios: Cabecera Municipal + Área Rural
        foreach ($municipios as $m) {
            $key = $m[0].'|'.$m[1];
            if (in_array($key, $municipiosConComunas)) continue;
            if (!isset($municipioIds[$key])) continue;
            $provId = $municipioIds[$key];
            $comunasInsert[] = [
                'nombre'      => 'Cabecera Municipal',
                'provincia_id'=> $provId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
            $comunasInsert[] = [
                'nombre'      => 'Área Rural',
                'provincia_id'=> $provId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        foreach (array_chunk($comunasInsert, 200) as $chunk) {
            DB::table('comunas')->insert($chunk);
        }
    }
}
