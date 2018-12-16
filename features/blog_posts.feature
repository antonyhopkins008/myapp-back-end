Feature: Manage blog posts
  @createSchema @blogPost @comment
  Scenario: Create a blog post
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "Hello a title",
      "content": "The content has to be more than 30 characters",
      "slug": "hello-a-title"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/BlogPost",
      "@id": "@string@",
      "@type": "BlogPost",
      "id": "@integer@",
      "title": "Hello a title",
      "content": "The content has to be more than 30 characters",
      "slug": "hello-a-title",
      "published": "@string@.isDateTime()",
      "author": {
        "@id": "@string@",
        "@type": "User",
        "username": "admin",
        "name": "Anton Pokhodun",
        "email": "admin@gmail.com",
        "roles": ["ROLE_SUPERADMIN"]
      },
      "comment": [],
      "images": []
    }
    """
  @comment
  Scenario: Add comment to the new blog post
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "content": "This is a new comment",
      "blogPost": "/api/blog_posts/101"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/Comment",
      "@id": "@string@",
      "@type": "Comment",
      "id": "@integer@",
      "content": "This is a new comment",
      "published": "@string@.isDateTime()",
      "author": {
          "@id": "/api/users/1",
          "@type": "User",
          "username": "admin",
          "name": "Anton Pokhodun",
          "email": "admin@gmail.com",
          "roles": [
              "ROLE_SUPERADMIN"
          ]
      },
      "blogPost": "/api/blog_posts/101"
    }
    """
  @test
  Scenario: Throw an error when comment body is empty
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "content": "",
      "blogPost": "/api/blog_posts/100"
    }
    """
#    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/ConstraintViolationList",
      "@type": "ConstraintViolationList",
      "hydra:title": "An error occurred",
      "hydra:description": "content: This value should not be blank.",
      "violations": [
          {
              "propertyPath": "content",
              "message": "This value should not be blank."
          }
      ]
    }
    """
  @test
  Scenario: Throw an error when comment is invalid
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "content": "Test body",
      "blogPost": "/api/blog_posts/102"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/ConstraintViolationList",
      "@type": "ConstraintViolationList",
      "hydra:title": "An error occurred",
      "hydra:description": "Item not found for \"/api/blog_posts/102\".",
      "violations": [
          {
              "propertyPath": "",
              "message": "Item not found for \"/api/blog_posts/102\"."
          }
      ]
}
    """
  @createSchema
  Scenario: Throws an error if blog post initial data is invalid
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "",
      "content": "",
      "slug": "hello-a-title"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/ConstraintViolationList",
      "@type": "ConstraintViolationList",
      "hydra:title": "An error occurred",
      "hydra:description": "title: This value should not be blank.\ncontent: This value should not be blank.",
      "violations": [
          {
              "propertyPath": "title",
              "message": "This value should not be blank."
          },
          {
              "propertyPath": "content",
              "message": "This value should not be blank."
          }
      ]
    }
    """
  @createSchema
  Scenario: Throws an error when user is not authenticated
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "",
      "content": "",
      "slug": "hello-a-title"
    }
    """
    Then the response status code should be 401
