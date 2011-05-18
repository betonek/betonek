<?php

$examples = array(
	"title_view" => array(
		"type"      => "book",
		"title"     => "Jaś i Małgosia w domku Baby Jagi",
		"title_id"  => 8,
		"author"    => "Jan Brzechwa",
		"author_id" => 3,
		"owners"    => array(
			array(
				"item_id"    => 13,
				"user_id"    => 5,
				"user"       => "Jan Kowalski",
				"user_email" => "kowalski@gmail.com"
			),
			array(
				"item_id"    => 18,
				"user_id"    => 3,
				"user"       => "Ewa Nowak",
				"user_email" => "ewanowa@yahoo.com"
			)
		),
		"is_owner" => 1,
		"average_mark" => 7.56,
		"comments" => array(
			array(
				"comment"    => "Ta książka zmieniła moje życie!\nPozdro",
				"user_id"    => 3,
				"user"       => "Ewa Nowak",
				"user_email" => "ewanowa@yahoo.com"
			)
		)
	),

	"title_rate" => array(
		"title_id" => 0,
		"mark"     => 5
	),

	"title_comment" => array(
		"title_id" => 0,
		"comment"  => "Bla bla\nBla!\n\nBla blaaaa!"
	),

	"item_delete" => array(
		"title_id" => 0
	),

	"item_add" => array(
		"title_suggestions" => array(
			array(
				"title"     => "A może nie wiesz jaki tytuł?",
				"title_id"  => 134,
				"author"    => "Włodzimierz Lepiejpiszący",
				"author_id" => 15
			),
			array(
				"title"     => "A może wybierz dobrą ksiązkę w 24h? Poradnik.",
				"title_id"  => 34,
				"author"    => "Konstantyn Kontestujący",
				"author_id" => 14
			)
		),
		"author_suggestions" => array(
			array("author" => "Włodzimierz Lepiejpiszący", "author_id" => 15),
			array("author" => "Włodzimierz Lepiejmyślący", "author_id" => 12),
			array("author" => "Włodzimierz Lepiejmówiący", "author_id" => 123)
		)
	),

	"item_add_final" => array(
		"item_id"   => 91,
		"title_id"  => 8,
		"author_id" => 3
	),

	"author_search" => array(
		"query"     => "",
		"authors"   => array(
			array("author" => "Jan Brzechwa", "author_id" => 3),
			array("author" => "Włodzimierz Lepiejpiszący", "author_id" => 15),
			array("author" => "Włodzimierz Lepiejmyślący", "author_id" => 12),
			array("author" => "Włodzimierz Lepiejmówiący", "author_id" => 123)
		)
	)
);
