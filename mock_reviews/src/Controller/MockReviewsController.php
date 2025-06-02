<?php

namespace Drupal\mock_reviews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class MockReviewsController extends ControllerBase {

  public function getReviews() {
    $reviews = [
      [
        'id' => '1',
        'company' => 'Example Corp',
        'user' => 'John D.',
        'rating' => 5,
        'title' => 'Excellent service!',
        'content' => 'The customer service was fantastic and the product arrived earlier than expected. Highly recommend!',
        'date' => '2023-05-15',
        'verified' => true,
      ],
      [
        'id' => '2',
        'company' => 'Example Corp',
        'user' => 'Sarah M.',
        'rating' => 4,
        'title' => 'Great product',
        'content' => 'Very happy with my purchase. The quality is excellent, though delivery took a bit longer than estimated.',
        'date' => '2023-04-22',
        'verified' => true,
      ],
      [
        'id' => '3',
        'company' => 'Example Corp',
        'user' => 'Robert T.',
        'rating' => 3,
        'title' => 'Average experience',
        'content' => 'Product was okay but customer service was slow to respond to my questions. Might buy again if improved.',
        'date' => '2023-03-10',
        'verified' => false,
      ],
      [
        'id' => '4',
        'company' => 'Example Corp',
        'user' => 'Emma K.',
        'rating' => 5,
        'title' => 'Will definitely buy again',
        'content' => 'Absolutely love this company! They went above and beyond to make sure I was satisfied.',
        'date' => '2023-02-28',
        'verified' => true,
      ],
      [
        'id' => '5',
        'company' => 'Example Corp',
        'user' => 'Michael B.',
        'rating' => 2,
        'title' => 'Disappointed',
        'content' => 'The product didn\'t meet my expectations based on the description. Returning it was a hassle.',
        'date' => '2023-01-15',
        'verified' => true,
      ],
    ];

    // Add some Trustpilot-like metadata
    $response_data = [
      'reviews' => $reviews,
      'stats' => [
        'average_rating' => 3.8,
        'total_reviews' => count($reviews),
        'rating_distribution' => [
          5 => 2,
          4 => 1,
          3 => 1,
          2 => 1,
          1 => 0,
        ],
      ],
      'company' => [
        'name' => 'Example Corp',
        'trustscore' => 3.8,
        'reviews_count' => count($reviews),
        'website' => 'https://www.example.com',
      ],
    ];

    return new JsonResponse($response_data);
  }
}