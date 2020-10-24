Feature: Manage blog posts
  @createSchema
  Scenario: Create a blog post
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "Hello World"
      "content": "This is a dummy content"
      "slug": "hello-world"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON