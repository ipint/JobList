<?php

namespace Database\Seeders;

use App\Models\UkCounty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UkCountySeeder extends Seeder
{
    public function run(): void
    {
        $counties = [
            'England' => [
                'Bedfordshire',
                'Berkshire',
                'Bristol',
                'Buckinghamshire',
                'Cambridgeshire',
                'Cheshire',
                'City of London',
                'Cornwall',
                'Cumbria',
                'Derbyshire',
                'Devon',
                'Dorset',
                'County Durham',
                'East Riding of Yorkshire',
                'East Sussex',
                'Essex',
                'Gloucestershire',
                'Greater London',
                'Greater Manchester',
                'Hampshire',
                'Herefordshire',
                'Hertfordshire',
                'Isle of Wight',
                'Kent',
                'Lancashire',
                'Leicestershire',
                'Lincolnshire',
                'Merseyside',
                'Norfolk',
                'North Yorkshire',
                'Northamptonshire',
                'Northumberland',
                'Nottinghamshire',
                'Oxfordshire',
                'Rutland',
                'Shropshire',
                'Somerset',
                'South Yorkshire',
                'Staffordshire',
                'Suffolk',
                'Surrey',
                'Tyne and Wear',
                'Warwickshire',
                'West Midlands',
                'West Sussex',
                'West Yorkshire',
                'Wiltshire',
                'Worcestershire',
            ],
            'Wales' => [
                'Clwyd',
                'Dyfed',
                'Gwent',
                'Gwynedd',
                'Mid Glamorgan',
                'Powys',
                'South Glamorgan',
                'West Glamorgan',
            ],
            'Scotland' => [
                'Aberdeenshire',
                'Angus',
                'Argyll',
                'Ayrshire',
                'Banffshire',
                'Berwickshire',
                'Caithness',
                'Clackmannanshire',
                'Dumfriesshire',
                'Dunbartonshire',
                'East Lothian',
                'Fife',
                'Inverness-shire',
                'Kincardineshire',
                'Kinross-shire',
                'Kirkcudbrightshire',
                'Lanarkshire',
                'Midlothian',
                'Moray',
                'Nairnshire',
                'Orkney',
                'Peeblesshire',
                'Perthshire',
                'Renfrewshire',
                'Ross-shire',
                'Roxburghshire',
                'Selkirkshire',
                'Shetland',
                'Stirlingshire',
                'Sutherland',
                'West Lothian',
                'Wigtownshire',
            ],
            'Northern Ireland' => [
                'Antrim',
                'Armagh',
                'Down',
                'Fermanagh',
                'Londonderry',
                'Tyrone',
            ],
        ];

        $records = [];
        $order = 1;

        foreach ($counties as $nation => $items) {
            foreach ($items as $county) {
                $records[] = [
                    'name' => $county,
                    'slug' => Str::slug($county),
                    'nation' => $nation,
                    'display_order' => $order++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        UkCounty::query()->upsert(
            $records,
            ['slug'],
            ['name', 'nation', 'display_order', 'is_active', 'updated_at'],
        );
    }
}
