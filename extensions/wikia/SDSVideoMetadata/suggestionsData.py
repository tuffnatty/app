#/usr/bin/python
# -*- coding: utf-8 -*- 

import httplib
import json
import time

server = 'dev-arturd:9494'
path = '/api/v0.1/'
object_url = 'http://sds.wikia.com/'
collection = 'sds'

data = {
	'wikia:VideoGame': [
		{
			'shortId': 'ufo_enemy_unknown',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'UFO: Enemy Unknown',
			'schema:datePublished': '1995-10-25T00:00:00Z',
			'schema:genre': 'Strategy game',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage01'}],
		},
		{
			'shortId': 'the_witcher',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'The Witcher',
			'schema:datePublished': '2007-10-26T00:00:00Z',
			'schema:genre': 'Action role-playing',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage02'}],
		},
		{
			'shortId': 'morrowind',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'The Elder Scrolls III: Morrowind',
			'schema:datePublished': '2002-05-01T00:00:00Z',
			'schema:genre': 'Action role-playing',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage03'}],
		},
		{
			'shortId': 'call_of_duty',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty',
			'schema:datePublished': '2003-10-29T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage04'}],
		},
		{
			'shortId': 'call_of_duty_2',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty 2',
			'schema:datePublished': '2005-10-25T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage05'}],
		},
		{
			'shortId': 'call_of_duty_3',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty 3',
			'schema:datePublished': '2006-11-07T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage06'}],
		},
		{
			'shortId': 'call_of_duty_4_modern_warfare',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty 4: Modern Warfare',
			'schema:datePublished': '2007-11-07T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage07'}],
		},
		{
			'shortId': 'call_of_duty_4_modern_warfare_2',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty: Modern Warfare 2',
			'schema:datePublished': '2009-02-11T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage08'}],
		},
		{
			'shortId': 'call_of_duty_world_at_war',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty: World at War',
			'schema:datePublished': '2008-06-09T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage09'}],
		},
		{
			'shortId': 'call_of_duty_black_ops',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/VideoGame',
			'schema:name': 'Call of Duty: Black Ops',
			'schema:datePublished': '2010-11-09T00:00:00Z',
			'schema:genre': 'FPS',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage10'}],
		},
	],
	'schema:AudioObject': [
		{
			'shortId': 'sound_track_1',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 1',
		},
		{
			'shortId': 'sound_track_2',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 2',
		},
		{
			'shortId': 'sound_track_3',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 3',
		},
		{
			'shortId': 'sound_track_4',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 4',
		},
		{
			'shortId': 'sound_track_5',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 5',
		},
		{
			'shortId': 'sound_track_6',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 6',
		},
		{
			'shortId': 'sound_track_7',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 7',
		},
		{
			'shortId': 'sound_track_8',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 8',
		},
		{
			'shortId': 'sound_track_9',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 9',
		},
		{
			'shortId': 'sound_track_10',
			'rdf:type': 'http://schema.org/AudioObject',
			'schema:name': 'Sound Track 10',
		},
	],
	'schema:TVSeries': [
		{
			'shortId': 'greys_anatomy',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "Grey's Anatomy",
		},
		{
			'shortId': 'the_walking_dead',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "The Walking Dead",
		},
		{
			'shortId': 'the_big_bang_theory',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "The Big Bang Theory",
			'schema:season': [{'id:': object_url + collection + '/the_big_bang_theory_1'},
				{'id:': object_url + collection + '/the_big_bang_theory_2'},
				{'id:': object_url + collection + '/the_big_bang_theory_3'},
				{'id:': object_url + collection + '/the_big_bang_theory_4'},
				{'id:': object_url + collection + '/the_big_bang_theory_5'},
				{'id:': object_url + collection + '/the_big_bang_theory_6'},
			]
		},
		{
			'shortId': 'the_vampire_diaries',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "The Vampire Diaries",
		},
		{
			'shortId': 'how_i_met_your_mother',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "How I Met Your Mother",
		},
		{
			'shortId': 'supernatural',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "Supernatural",
		},
		{
			'shortId': 'the_mentalist',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "The Mentalist",
		},
		{
			'shortId': 'ncis',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "NCIS",
		},
		{
			'shortId': 'criminal_minds',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "Criminal Minds",
		},
		{
			'shortId': 'person_of_interest',
			'rdf:type': 'http://schema.org/TVSeries',
			'schema:name': "Person of Interest",
		},
	],
	'schema:TVSeason': [
		{
			'shortId': 'the_big_bang_theory_1',
			'rdf:type': 'http://schema.org/TVSeason',
			'schema:name': 'The Big Bang Theory Season 1',
			'schema:startDate': '2007-09-24T00:00:00Z',
			'schema:endDate': '2008-05-19T00:00:00Z',
			'schema:seasonNumber': 1,
			'schema:partOfTVSeries': [{'id': object_url + collection + '/the_big_bang_theory'}],
		},
		{
			'shortId': 'the_big_bang_theory_2',
			'rdf:type': 'http://schema.org/TVSeason',
			'schema:name': 'The Big Bang Theory Season 2',
			'schema:startDate': '2008-09-22T00:00:00Z',
			'schema:endDate': '2009-05-11T00:00:00Z',
			'schema:seasonNumber': 2,
			'schema:partOfTVSeries': [{'id': object_url + collection + '/the_big_bang_theory'}],
		},
		{
			'shortId': 'the_big_bang_theory_3',
			'rdf:type': 'http://schema.org/TVSeason',
			'schema:name': 'The Big Bang Theory Season 3',
			'schema:startDate': '2009-09-23T00:00:00Z',
			'schema:endDate': '2010-05-24T00:00:00Z',
			'schema:seasonNumber': 3,
			'schema:partOfTVSeries': [{'id': object_url + collection + '/the_big_bang_theory'}],
		},
		{
			'shortId': 'the_big_bang_theory_4',
			'rdf:type': 'http://schema.org/TVSeason',
			'schema:name': 'The Big Bang Theory Season 4',
			'schema:startDate': '2010-09-23T00:00:00Z',
			'schema:endDate': '2011-05-19T00:00:00Z',
			'schema:seasonNumber': 4,
			'schema:partOfTVSeries': [{'id': object_url + collection + '/the_big_bang_theory'}],
		},
		{
			'shortId': 'the_big_bang_theory_5',
			'rdf:type': 'http://schema.org/TVSeason',
			'schema:name': 'The Big Bang Theory Season 5',
			'schema:startDate': '2011-09-22T00:00:00Z',
			'schema:endDate': '2012-05-10T00:00:00Z',
			'schema:seasonNumber': 5,
			'schema:partOfTVSeries': [{'id': object_url + collection + '/the_big_bang_theory'}],
		},
		{
			'shortId': 'the_big_bang_theory_6',
			'rdf:type': 'http://schema.org/TVSeason',
			'schema:name': 'The Big Bang Theory Season 6',
			'schema:startDate': '2012-09-27T00:00:00Z',
			'schema:endDate': '2013-05-19T00:00:00Z',
			'schema:seasonNumber': 6,
			'schema:partOfTVSeries': [{'id': object_url + collection + '/the_big_bang_theory'}],
		},
	],
	'schema:Movie': [
		{
			'shortId': 'iron_man_3',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Iron Man 3',
			'schema:genre': 'Action',
			'schema:copyrightYear': 2013,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage01'}],
		},
		{
			'shortId': 'life_of_pi',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Life of Pi',
			'schema:genre': 'Adventure',
			'schema:copyrightYear': 2012,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage02'}],
		},
		{
			'shortId': 'the_lion_king',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'The Lion King',
			'schema:genre': 'Animation',
			'schema:copyrightYear': 1994,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage03'}],
		},
		{
			'shortId': 'hitchcock',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Hitchcock',
			'schema:genre': 'Biography',
			'schema:copyrightYear': 2012,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage04'}],
		},
		{
			'shortId': 'looper',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Looper',
			'schema:genre': 'Crime',
			'schema:copyrightYear': 2012,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage05'}],
		},
		{
			'shortId': 'argo',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Argo',
			'schema:genre': 'Drama',
			'schema:copyrightYear': 2012,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage06'}],
		},
		{
			'shortId': 'total_recall_2012',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Total Recall',
			'schema:genre': 'Sci-Fi',
			'schema:copyrightYear': 2012,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage07'}],
		},
		{
			'shortId': 'total_recall_1990',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'Total Recall',
			'schema:genre': 'Sci-Fi',
			'schema:copyrightYear': 1990,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage08'}],
		},
		{
			'shortId': 'true_grit',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'True Grit',
			'schema:genre': 'Western',
			'schema:copyrightYear': 2010,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage09'}],
			'schema:actors': [
				{'id': object_url + collection + '/jeff_bridges'},
				{'id': object_url + collection + '/hailee_steinfeld'},
				{'id': object_url + collection + '/matt_damon'},
				{'id': object_url + collection + '/josh_brolin'},
				{'id': object_url + collection + '/barry_pepper'},
				{'id': object_url + collection + '/dakin_matthews'},
			]
		},
		{
			'shortId': 'the_cabin_in_the_woods',
			'rdf:type': 'http://schema.org/Movie',
			'schema:name': 'The Cabin in the Woods',
			'schema:genre': 'Horror',
			'schema:copyrightYear': 2011,
			'schema:associatedMedia': [{'id': object_url + collection + '/sugestionsImage10'}],
		},
	],
	'schema:MusicRecording': [
		{
			'shortId': 'czarne_slonce', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Czarne Słońca',
			'schema:byArtist': [{'id': object_url + collection + '/kult'}],
		},
		{
			'shortId': 'her_ghost_in_the_fog',
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Her ghost in the fog',
			'schema:byArtist': [{'id': object_url + collection + '/cradle_of_filth'}],
		},
		{
			'shortId': 'unforgiven', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Unforgiven',
			'schema:byArtist': [{'id': object_url + collection + '/metallica'}],
		},
		{
			'shortId': 'octavarium', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Octavarium',
			'schema:byArtist': [{'id': object_url + collection + '/in_flames'}],
		},
		{
			'shortId': 'jester_race', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Jester Race',
			'schema:byArtist': [{'id': object_url + collection + '/dream_theater'}],
		},
		{
			'shortId': 'ashes_to_ashes', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Ashes to Ashes',
			'schema:byArtist': [{'id': object_url + collection + '/faith_no_more'}],
		},
		{
			'shortId': 'bohemian_rapsody', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Bohemian Rapsody',
			'schema:byArtist': [{'id': object_url + collection + '/queen'}],
		},
		{
			'shortId': 'fugative', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Fugative',
			'schema:byArtist': [{'id': object_url + collection + '/iron_maiden'}],
		},
		{
			'shortId': 'estranged', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'Estranged',
			'schema:byArtist': [{'id': object_url + collection + '/guns_n_roses'}],
		},
		{
			'shortId': 'in_bloom', 
			'rdf:type': 'http://schema.org/MusicRecording',
			'schema:name': 'In Bloom',
			'schema:byArtist': [{'id': object_url + collection + '/nirvana'}],
		},
	],
	'schema:MusicGroup': [
		{
			'shortId': 'kult', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Kult'
		},
		{
			'shortId': 'cradle_of_filth', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Cradle of Filth'
		},
		{
			'shortId': 'metallica', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Metallica'
		},
		{
			'shortId': 'in_flames', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'In Flames'
		},
		{
			'shortId': 'dream_theater', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Drem Theater'
		},
		{
			'shortId': 'faith_no_more', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Faith No More'
		},
		{
			'shortId': 'queen', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Queen'
		},
		{
			'shortId': 'iron_maiden', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Iron Maiden'
		},
		{
			'shortId': 'guns_n_roses', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': "Guns'n'Roses"
		},
		{
			'shortId': 'nirvana', 
			'rdf:type': 'http://schema.org/MusicGroup',
			'schema:name': 'Nirvana'
		},
	],
	'schema:Ogranization': [
		{
			'shortId': 'sony', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Sony'
		},
		{
			'shortId': 'wikia', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Wikia'
		},
		{
			'shortId': 'google', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Google'
		},
		{
			'shortId': 'amazon', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Amazon'
		},
		{
			'shortId': 'activision', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Activision'
		},
		{
			'shortId': 'valve', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Valve'
		},
		{
			'shortId': 'facebook', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'Facebook'
		},
		{
			'shortId': 'ign', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'IGN'
		},
		{
			'shortId': 'id_software', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'ID Software'
		},
		{
			'shortId': 'cd_project', 
			'rdf:type': 'http://schema.org/Ogranization',
			'schema:name': 'CD Project'
		},
	],
	'schema:Place': [
		{
			'shortId': 'stary_rynek',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Stary rynek',
			'photo': [{'id' : object_url + collection + '/sugestionsImage01'}]
		},
		{
			'shortId': 'wilda',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Wilda',
			'photo': [{'id' : object_url + collection + '/sugestionsImage02'}]
		},
		{
			'shortId': 'debiec',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Dębiec',
			'photo': [{'id' : object_url + collection + '/sugestionsImage03'}]
		},
		{
			'shortId': 'lazarz',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Łazarz',
			'photo': [{'id' : object_url + collection + '/sugestionsImage04'}]
		},
		{
			'shortId': 'grunwald',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Grunwald',
			'photo': [{'id' : object_url + collection + '/sugestionsImage05'}]
		},
		{
			'shortId': 'piatkowo',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Piątkowo',
			'photo': [{'id' : object_url + collection + '/sugestionsImage06'}]
		},
		{
			'shortId': 'winogrady',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Winogrady',
			'photo': [{'id' : object_url + collection + '/sugestionsImage07'}]
		},
		{
			'shortId': 'rataje',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Rataje',
			'photo': [{'id' : object_url + collection + '/sugestionsImage08'}]
		},
		{
			'shortId': 'jezyce',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Jeżyce',
			'photo': [{'id' : object_url + collection + '/sugestionsImage09'}]
		},
		{
			'shortId': 'staroleka',
			'rdf:type': 'http://schema.org/Place',
			'schema:name': 'Starołęka',
			'photo': [{'id' : object_url + collection + '/sugestionsImage10'}]
		},
	],
	'schema:Recipe': [
		{
			'shortId' : 'zupa_pieczarkowa',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Zupa pieczarkowa',
			'schema:image': 'http://images4.wikia.nocookie.net/__cb20120702093321/nandytest/images/thumb/8/81/Pieczarkowa.jpg/100px-Pieczarkowa.jpg',
			'schema:description': 'Opis zupy pieczarkowej'
		},
		{
			'shortId' : 'dorsz',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Dorsz',
			'schema:image': 'http://images4.wikia.nocookie.net/__cb20120623103108/nandytest/images/thumb/4/4d/Dorsz.jpg/100px-Dorsz.jpg',
			'schema:description': 'Opis dorsza'
		},
		{
			'shortId' : 'kotlet_rybny',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Kotlet rybny',
			'schema:image': 'http://images3.wikia.nocookie.net/__cb20130215100743/nandytest/images/thumb/d/da/Kotlet_rybny.jpg/100px-Kotlet_rybny.jpg',
			'schema:description': 'Opis kotleta rybnego'
		},
		{
			'shortId' : 'pierogi_ruskie',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Pierogi ruskie',
			'schema:image': 'http://images2.wikia.nocookie.net/__cb20120623103735/nandytest/images/thumb/9/9c/Pierogi_ruskie.jpg/100px-Pierogi_ruskie.jpg',
			'schema:description': 'Opis pierogów ruskich'
		},
		{
			'shortId' : 'bar_salatkowy',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Bar sałatkowy',
			'schema:image': 'http://images1.wikia.nocookie.net/__cb20120623103049/nandytest/images/thumb/4/45/Bar_salatkowy.jpg/100px-Bar_salatkowy.jpg',
			'schema:description': 'Opis baru sałatkowego'
		},
		{
			'shortId' : 'zestaw_piatkowy',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Kotlet schabowy (mały), szpinak, jajko',
			'schema:image': 'http://images3.wikia.nocookie.net/__cb20120623103606/nandytest/images/thumb/a/a2/Maly_schabowy_szpinak_jajko.jpg/100px-Maly_schabowy_szpinak_jajko.jpg',
			'schema:description': 'Opis zestawu piątkowego'
		},
		{
			'shortId' : 'pyry_z_gzikiem',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Pyry z gzikiem',
			'schema:image': 'http://images4.wikia.nocookie.net/__cb20130219090929/nandytest/images/thumb/5/53/Pyry_z_gzikiem_%28male%29.jpg/100px-Pyry_z_gzikiem_%28male%29.jpg',
			'schema:description': 'Opis pyr z gzikiem'
		},
		{
			'shortId' : 'nalesniki_z_serem_na_slodko',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Naleśniki z serem na słodko',
			'schema:image': 'http://images2.wikia.nocookie.net/__cb20120808090015/nandytest/images/thumb/a/ae/Nalesniki.jpg/100px-Nalesniki.jpg',
			'schema:description': 'Opis naleśników na słodko'
		},
		{
			'shortId' : 'zeberka',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Żeberka',
			'schema:image': 'http://images4.wikia.nocookie.net/__cb20120623113421/nandytest/images/thumb/1/1a/Zeberka.jpg/100px-Zeberka.jpg',
			'schema:description': 'Opis żeberek'
		},
		{
			'shortId' : 'pieczen',
			'rdf:type': 'http://schema.org/Recipe',
			'schema:name': 'Pieczeń',
			'schema:image': 'http://images2.wikia.nocookie.net/__cb20120623103707/nandytest/images/thumb/4/49/Pieczen.jpg/100px-Pieczen.jpg',
			'schema:description': 'Opis pieczeni'
		},
	],
	'schema:ImageObject': [
		{
			'shortId' : 'sugestionsImage01',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images4.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/8/80/Wikia-Visualization-Main%2Cl5r.png/160px-0%2C480%2C19%2C319-Wikia-Visualization-Main%2Cl5r.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'l5r'
		},
		{
			'shortId' : 'sugestionsImage02',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images2.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/4/49/Wikia-Visualization-Main%2Chimym.png/160px-0%2C320%2C19%2C219-Wikia-Visualization-Main%2Chimym.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'himym'
		},		
		{
			'shortId' : 'sugestionsImage03',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images3.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/2/21/Wikia-Visualization-Main%2Csonsofanarchy.png/160px-0%2C480%2C19%2C319-Wikia-Visualization-Main%2Csonsofanarchy.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'sonsofanarchy'
		},		
		{
			'shortId' : 'sugestionsImage04',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images3.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/7/79/Wikia-Visualization-Main%2Creddeadredemption.png/160px-0%2C320%2C19%2C219-Wikia-Visualization-Main%2Creddeadredemption.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'reddeadredemption'
		},		
		{
			'shortId' : 'sugestionsImage05',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images3.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/3/3b/Wikia-Visualization-Main%2Ctwistedmetal.png/160px-0%2C480%2C33%2C333-Wikia-Visualization-Main%2Ctwistedmetal.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'twistedmetal'
		},		
		{
			'shortId' : 'sugestionsImage06',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images4.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/0/06/Wikia-Visualization-Main%2Cclashofclans.png/160px-0%2C903%2C37%2C601-Wikia-Visualization-Main%2Cclashofclans.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'clashofclans'
		},		
		{
			'shortId' : 'sugestionsImage07',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images2.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/c/cb/Wikia-Visualization-Main%2Cikusabaanimation.png/160px-0%2C650%2C28%2C434-Wikia-Visualization-Main%2Cikusabaanimation.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'ikusabaanimation'
		},		
		{
			'shortId' : 'sugestionsImage08',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images2.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/7/75/Wikia-Visualization-Main%2Ctouhou.png/160px-0%2C320%2C19%2C219-Wikia-Visualization-Main%2Ctouhou.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'touhou'
		},		
		{
			'shortId' : 'sugestionsImage09',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images2.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/e/e5/Wikia-Visualization-Main%2Cdofus.png/160px-0%2C320%2C19%2C219-Wikia-Visualization-Main%2Cdofus.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'dofus'
		},		
		{
			'shortId' : 'sugestionsImage10',
			'rdf:type': 'http://schema.org/ImageObject',
			'schema:contentURL': 'http://images2.wikia.nocookie.net/__cb62043/wikiaglobal/images/thumb/1/14/Wikia-Visualization-Main%2Ctheformula1.png/160px-341%2C1054%2C0%2C445-Wikia-Visualization-Main%2Ctheformula1.png',
			'schema:height': 160,
			'schema:width': 100,
			'schema:name': 'theformula1'
		},		
	],
	'schema:Person': [
		{
			'shortId': 'jeff_bridges',
			'rdf:type': 'http://schema.org/Person',
			'schema:name': 'Jeff Bridges',
			'schema:image': 'http://ia.media-imdb.com/images/M/MV5BNTU1NjM4MDYzMl5BMl5BanBnXkFtZTcwMjIwMjMyMw@@._V1._SY314_CR10,0,214,314_.jpg',
			'schema:birthDate': '1949-12-04T00:00:00Z',
		},
		{
			'shortId': 'hailee_steinfeld',
			'rdf:type': 'http://schema.org/Person',
			'schema:name': 'Hailee Steinfeld',
			'schema:image': 'http://ia.media-imdb.com/images/M/MV5BMjMwMzAzNTUxNF5BMl5BanBnXkFtZTcwNDQyNDE3OA@@._V1._SY314_CR10,0,214,314_.jpg',
			'schema:birthDate': '1996-12-11T00:00:00Z',
		},
		{
			'shortId': 'matt_damon',
			'rdf:type': 'http://schema.org/Person',
			'schema:name': 'Matt Damon',
			'schema:image': 'http://ia.media-imdb.com/images/M/MV5BMTM0NzYzNDgxMl5BMl5BanBnXkFtZTcwMDg2MTMyMw@@._V1._SY314_CR10,0,214,314_.jpg',
			'schema:birthDate': '1970-10-08T00:00:00Z',
		},
		{
			'shortId': 'josh_brolin',
			'rdf:type': 'http://schema.org/Person',
			'schema:name': 'Josh Brolin',
			'schema:image': 'http://ia.media-imdb.com/images/M/MV5BMTQ1MzYyMjQ0Nl5BMl5BanBnXkFtZTcwMTA0ODkyMg@@._V1._SX214_CR0,0,214,314_.jpg',
			'schema:birthDate': '1968-02-12T00:00:00Z',
		},
		{
			'shortId': 'barry_pepper',
			'rdf:type': 'http://schema.org/Person',
			'schema:name': 'Barry Pepper',
			'schema:image': 'http://ia.media-imdb.com/images/M/MV5BMTg0MTEzMjcxM15BMl5BanBnXkFtZTcwMjEwMjMxNw@@._V1._SY314_CR1,0,214,314_.jpg',
			'schema:birthDate': '1970-04-04T00:00:00Z',
		},
		{
			'shortId': 'dakin_matthews',
			'rdf:type': 'http://schema.org/Person',
			'schema:name': 'Dakin Matthews',
			'schema:image': 'http://ia.media-imdb.com/images/M/MV5BMjI3MzE4Mzg2NF5BMl5BanBnXkFtZTcwMTA2MDMxOA@@._V1._SY314_CR6,0,214,314_.jpg',
			'schema:birthDate': '1940-11-07T00:00:00Z',
		},
	],
	'wikia:Character': [
		{
			'shortId': 'private_martin',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Private Martin',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage01'}],
		},
		{
			'shortId': 'sergeant_evans',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Sergeant Evans',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage02'}],
		},
		{
			'shortId': 'private_alexei_ivanovich_voronin',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Private Alexei Ivanovich Voronin',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage03'}],
		},
		{
			'shortId': 'captain_foley ',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Captain Foley ',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage04'}],
		},
		{
			'shortId': 'sergeant_moody',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Sergeant Moody',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage05'}],
		},
		{
			'shortId': 'private_elder',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Private Elder',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage06'}],
		},
		{
			'shortId': 'captain_price',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Captain Price',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage07'}],
		},
		{
			'shortId': 'sergeant_waters',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Sergeant Waters',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage08'}],
		},
		{
			'shortId': 'major_gerald_tiberius_ingram ',
			'rdf:type': 'http://sds.wikia.com/vocabs/wikia/Character',
			'schema:name': 'Major Gerald Tiberius Ingram ',
			'schema:photos': [{'id': object_url + collection + '/sugestionsImage09'}],
		},
	]
}

class SDSClient(object):
	def __init__(self, server, path, collection):
		self.server = server
		self.path = path
		self.collection = collection
	
	def call(self, method, shortId, body = None):
		conn = httplib.HTTPConnection(self.server)
		path = self.path + self.collection
		if shortId is not None:
			path += '/' + shortId
		conn.request(method, path + '?cb=' + str(time.time()), body)
		resp = conn.getresponse()
		if resp.status < 200 or resp.status >= 300:
			print  'delete path ' + path
			raise Exception('Invalid status code ' + str(resp.status) + ' while calling ' + method + ' ' + path)
		return resp.status, resp.read()
		
	def get(self, shortId):
		return self.call('GET', shortId)
	
	def delete(self, shortId):
	    return self.call('DELETE', shortId)
	
	def create(self, body):
		return self.call('POST', None, body)
		
	def modify(self, shortId, body):
		return self.call('PUT', shortId, body)

client = SDSClient(server, path, collection)

# remove previously created test data

not_removed = []
for obj_type, objects in data.items():
	for sds_object in objects:
		if 'shortId' in sds_object:
			for attempt in range(5):
				try:
					client.delete(sds_object['shortId'])
					print "Object " + obj_type + ' ' + sds_object['shortId'] + " removed"
					break
				except Exception, e:
					print e
			else:
				print "Could not delete object " + obj_type + ' ' + sds_object['shortId'] + ' ('+str(e)+')'
				not_removed.append(sds_object['shortId'])
		break
if len(not_removed) > 0:
	print "Note that those elements were not removed: " + str(not_removed)

# create the test data
not_created = []
for obj_type, objects in data.items():
	for sds_object in objects:
		if 'shortId' in sds_object:
			body = sds_object.copy()
			del(body['shortId'])
			body['id'] = object_url + collection + '/' + sds_object['shortId']
			for attempt in range(5):
				try:
					if not attempt:
						status, _ = client.create(json.dumps(body))
						print "Created object " + obj_type + ' ' + sds_object['shortId'] + ' (status code ' + str(status) + ')'
					else:
						status, _ = client.modify(sds_object['shortId'], json.dumps(body))
						print "Modified object " + obj_type + ' ' + sds_object['shortId'] + ' (status code ' + str(status) + ')'
					break
				except Exception, e:
					print e
			else:
				print "Did not create object " + obj_type + ' ' + sds_object['shortId'] + ' ('+str(e)+')'
				not_created.append(sds_object['shortId'])

if len(not_created) > 0:
	print "Note that those elements were not created: " + str(not_created)

