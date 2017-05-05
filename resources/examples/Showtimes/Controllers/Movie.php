<?php
namespace Mill\Examples\Showtimes\Controllers;

/**
 * Information on a specific movie.
 *
 * @api-label Movies
 */
class Movie
{
    /**
     * Return information on a specific movie.
     *
     * @api-label Get a single movie.
     *
     * @api-uri:private:alias {Movies} /movie/+id
     * @api-uriSegment {/movie/+id} {integer} id Movie ID
     *
     * @api-uri:public {Movies} /movies/+id
     * @api-uriSegment {/movies/+id} {integer} id Movie ID
     *
     * @api-return:public {object} \Mill\Examples\Showtimes\Representations\Movie
     * @api-return:public {notmodified} If no content has been modified since the supplied Last-Modified header.
     *
     * @api-throws:public {404} \Mill\Examples\Showtimes\Representations\Error If the movie could not be found.
     *
     * @api-version >=1.1.2
     * @api-contentType application/mill.example.movie
     *
     * @api-version <1.1.2
     * @api-contentType application/json
     */
    public function GET()
    {
        //
    }

    /**
     * Update a movies data.
     *
     * @api-label Update a movie.
     *
     * @api-uri:public {Movies} /movies/+id
     * @api-uriSegment {/movies/+id} {integer} id Movie ID
     *
     * @api-scope edit
     * @api-minVersion 1.1
     *
     * @api-param:public {string} name Name of the movie.
     * @api-param:public {string} description Description, or tagline, for the movie.
     * @api-param:public {string} runtime (optional) Movie runtime, in `HHhr MMmin` format.
     * @api-param:public {string} content_rating [G|PG|PG-13|R|NC-17|X|NR|UR] (optional) MPAA rating
     * @api-param:public {array} genres (optional) Array of movie genres.
     * @api-param:public {string} trailer (optional) Trailer URL
     * @api-param:public {string} director (optional) Name of the director.
     * @api-param:public {array} cast (optional) Array of names of the cast.
     * @api-param:public {boolean} is_kid_friendly (optional) Is this movie kid friendly?
     * @api-param:public {integer} rotten_tomatoes_score (optional) Rotten Tomatoes score
     *
     * @api-return:public {object} \Mill\Examples\Showtimes\Representations\Movie
     *
     * @api-throws:public {400} \Mill\Examples\Showtimes\Representations\Error If there is a problem with the
     *      request.
     * @api-throws:public {400} \Mill\Examples\Showtimes\Representations\Error If the IMDB URL could not be validated.
     * @api-throws:public {404} \Mill\Examples\Showtimes\Representations\Error If the movie could not be found.
     *
     * @api-version >=1.1.2
     * @api-contentType application/mill.example.movie
     *
     * @api-version <1.1.2
     * @api-contentType application/json
     *
     * @api-version >=1.1.1
     * @api-param:public {string} imdb (optional) IMDB URL
     */
    public function PATCH()
    {
        //
    }

    /**
     * Delete a movie.
     *
     * @api-label Delete a movie.
     *
     * @api-uri:private {Movies} /movies/+id
     * @api-uriSegment {/movies/+id} {integer} id Movie ID
     *
     * @api-contentType application/json
     * @api-scope delete
     *
     * @api-return:private {deleted}
     *
     * @api-throws:private {404} \Mill\Examples\Showtimes\Representations\Error If the movie could not be found.
     */
    public function DELETE()
    {
        //
    }
}
