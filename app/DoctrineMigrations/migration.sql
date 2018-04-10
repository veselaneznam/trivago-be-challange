DROP TABLE  IF EXISTS criteria ;

CREATE TABLE criteria
(
    id INTEGER PRIMARY KEY NOT NULL,
    name TEXT NOT NULL,
    alternative_name TEXT NOT NULL
);
CREATE UNIQUE INDEX UNIQ_B61F9B815E237E06 ON criteria (name);

INSERT INTO criteria VALUES
(null,"room","room,apartment,chamber"),
(null,"hotel","hotel,property,lodge,resort"),
(null,"staff","staff,service,personnel,crew,he,she"),
(null,"location","location,spot"),
(null,"breakfast","breakfast"),
(null,"bed","bed,sleep,quality,mattresses,linens"),
(null,"food","food,dinner,lunch"),
(null,"bathroom","bathroom,lavatory,shower,toilet,bath"),
(null,"restaurant","restaurant"),
(null,"pool","pool,spa,wellness"),
(null,"bar","bar"),
(null,"cosr","price,bill");


DROP TABLE  IF EXISTS hotel ;
CREATE TABLE hotel
(
    id INTEGER PRIMARY KEY NOT NULL,
    name TEXT NOT NULL
);
CREATE UNIQUE INDEX UNIQ_3535ED95E237E06 ON hotel (name);

INSERT INTO hotel VALUES
(null, 'Hilton'),
(null, 'Tesla'),
(null, 'Paris');


DROP TABLE  IF EXISTS negative ;
CREATE TABLE negative
(
    id INTEGER PRIMARY KEY NOT NULL,
    negative TEXT NOT NULL
);

INSERT INTO negative VALUES
  (null,"didn't work"),
  (null,"ancient"),
  (null,"cold"),
  (null,"tiny"),
  (null,"small"),
  (null,"hard"),
  (null,"uncomfortable"),
  (null,"torn"),
  (null,"Stay away"),
  (null,"old"),
  (null,"decrepit"),
  (null,"terrible"),
  (null,"broken"),
  (null,"junk"),
  (null,"awful"),
  (null,"worst"),
  (null,"disgusting"),
  (null,"falling out"),
  (null,"minty"),
  (null,"thin"),
  (null,"nightmare"),
  (null,"freezing"),
  (null,"didn't sleep"),
  (null,"rude"),
  (null,"undisciplined"),
  (null,"fell off"),
  (null,"rotten"),
  (null,"mess"),
  (null,"surly"),
  (null,"never"),
  (null,"not going to come back");



  DROP TABLE  IF EXISTS positive ;
CREATE TABLE positive
(
    id INTEGER PRIMARY KEY NOT NULL,
    positive TEXT NOT NULL
);

INSERT INTO positive VALUES
(null,"excellent"),
(null,"top"),
(null,"superb"),
(null,"fantastic"),
(null,"best"),
(null,"comfortable"),
(null,"perfect"),
(null,"love"),
(null,"going to come back"),
(null,"made our stay"),
(null,"was fun"),
(null,"not far from");

DROP TABLE  IF EXISTS review;
CREATE TABLE review
(
    id INTEGER PRIMARY KEY NOT NULL,
    hotel_id INTEGER DEFAULT NULL,
    review TEXT NOT NULL,
    total_score INTEGER NOT NULL,
    author TEXT NOT NULL,
    score_description TEXT NOT NULL,
    FOREIGN KEY (hotel_id) REFERENCES hotel (id) DEFERRABLE INITIALLY DEFERRED
);
CREATE UNIQUE INDEX IDX_794381C63243BB18 ON review (hotel_id);

INSERT INTO review VALUES
(NULL , 2, 'The hotel was nice and bad in the same time', 0, 'Vesela', ''),
(NULL , 1, 'Found this hotel by reading over tripadvisor while planning a little beach getaway. Really good price by the beach. James the front desk manager was really fun, he made our stay more fun than we thought it would be. We are going to come back with our friends soon.', 0, 'Vesela', ''),
(NULL , 3, 'Across the road from Santa Monica Pier is exactly where you want to be when visiting Santa Monica, as well as not far from lots of shops and restaurants/bars.Hotel itself is very new & modern, rooms were great. Comfortable beds & possibly the best shower ever!', 0, 'Vesela', ''),
(NULL , 1, 'I have stayed here 4 or 5 times while visiting LA. Despite travelling all over the world and staying in some of the best international hotels ( for work), Hotel Caliornia is one of my absolute favourites. Perfect location, right on the beach. I love the way you can just open your door and be outside, no elevators, corridors big glass windows. The ambience is so nice, retro perfect. As for the staff, I have had consistently superb service, with much more personal service and attention to detail than is usual in bigger hotels. Aaron and Katy were just two who have been exemplary this time but really everyone is terrific. Can''t recommend it highly enough.', 0, 'Vesela', ''),
(NULL , 3, 'Terrible. Old, not quite clean. Lost my reservation, then "found" a smaller room, for the same price, of course. Noisy. Absolutely no parking, unless you luck out for the $10 spaces (of which there are 12). Water in bathroom sink would not turn off. Not hair dryer, no iron in room. Miniscule shower- better be thin to use it!', 0, 'Vesela', ''),
(NULL , 1, 'I was excited to stay at this Hotel. It looked cute and was reasonableIt turned out to be terrible. We were woken up both mornings at 5:45 AM by noisy neighbors. The shower was clogged up...the room was sooooo small we kept tripping over each other. The saving grace was the pool at the Loews next door. I wish we had paid an extra $50 and stayed at a nicer place. This motel should cost no more than $99 a night.. ', 0, 'Vesela', '');


