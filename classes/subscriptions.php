<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Pdfannotator subscription manager.
 *
 * @package    mod_pdfannotator
 * @copyright  2021 Luca Bösch <luca.boesch@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_pdfannotator;

defined('MOODLE_INTERNAL') || die();

/**
 * Pdfannotator subscription manager.
 *
 * @copyright  2021 Luca Bösch <luca.boesch@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscriptions {

    /**
     * The status value for an unsubscribed discussion.
     *
     * @var int
     */
    const PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED = -1;

    /**
     * The subscription cache for pdfannotators.
     *
     * The first level key is the user ID
     * The second level is the pdfannotator ID
     * The Value then is bool for subscribed of not.
     *
     * @var array[] An array of arrays.
     */
    protected static $pdfannotatorcache = array();

    /**
     * The list of pdfannotators which have been wholly retrieved for the pdfannotator subscription cache.
     *
     * This allows for prior caching of an entire pdfannotator to reduce the
     * number of DB queries in a subscription check loop.
     *
     * @var bool[]
     */
    protected static $fetchedpdfannotators = array();

    /**
     * The subscription cache for pdfannotator discussions.
     *
     * The first level key is the user ID
     * The second level is the pdfannotator ID
     * The third level key is the discussion ID
     * The value is then the users preference (int)
     *
     * @var array[]
     */
    protected static $pdfannotatordiscussioncache = array();

    /**
     * The list of pdfannotators which have been wholly retrieved for the pdfannotator discussion subscription cache.
     *
     * This allows for prior caching of an entire pdfannotator to reduce the
     * number of DB queries in a subscription check loop.
     *
     * @var bool[]
     */
    protected static $discussionfetchedpdfannotators = array();

    /**
     * Whether a user is subscribed to this pdfannotator, or a discussion within
     * the pdfannotator.
     *
     * If a discussion is specified, then report whether the user is
     * subscribed to posts to this particular discussion, taking into
     * account the pdfannotator preference.
     *
     * If it is not specified then only the pdfannotator preference is considered.
     *
     * @param int $userid The user ID
     * @param \stdClass $pdfannotator The record of the pdfannotator to test
     * @param int $discussionid The ID of the discussion to check
     * @param object $cm The coursemodule record. If not supplied, this will be calculated using get_fast_modinfo instead.
     * @return bool
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function is_subscribed($userid, $pdfannotator, $discussionid = null, $cm = null) {
        // If pdfannotator is force subscribed and has allowforcesubscribe, then user is subscribed.
        if (self::is_forcesubscribed($pdfannotator)) {
            if (!$cm) {
                $cm = get_fast_modinfo($pdfannotator->course)->instances['pdfannotator'][$pdfannotator->id];
            }
            if (has_capability('mod/pdfannotator:allowforcesubscribe', \context_module::instance($cm->id), $userid)) {
                return true;
            }
        }

        if ($discussionid === null) {
            return self::is_subscribed_to_pdfannotator($userid, $pdfannotator);
        }

        $subscriptions = self::fetch_discussion_subscription($pdfannotator->id, $userid);

        // Check whether there is a record for this discussion subscription.
        if (isset($subscriptions[$discussionid])) {
            return ($subscriptions[$discussionid] != self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED);
        }

        return self::is_subscribed_to_pdfannotator($userid, $pdfannotator);
    }

    /**
     * Whether a user is subscribed to this pdfannotator.
     *
     * @param int $userid The user ID
     * @param \stdClass $pdfannotator The record of the pdfannotator to test
     * @return boolean
     */
    protected static function is_subscribed_to_pdfannotator($userid, $pdfannotator) {
        return self::fetch_subscription_cache($pdfannotator->id, $userid);
    }

    /**
     * Helper to determine whether a pdfannotator has it's subscription mode set
     * to forced subscription.
     *
     * @param \stdClass $pdfannotator The record of the pdfannotator to test
     * @return bool
     */
    public static function is_forcesubscribed($pdfannotator) {
        return ($pdfannotator->forcesubscribe == pdfannotator_FORCESUBSCRIBE);
    }

    /**
     * Helper to determine whether a pdfannotator has it's subscription mode set to disabled.
     *
     * @param \stdClass $pdfannotator The record of the pdfannotator to test
     * @return bool
     */
    public static function subscription_disabled($pdfannotator) {
        return ($pdfannotator->forcesubscribe == pdfannotator_DISALLOWSUBSCRIBE);
    }

    /**
     * Helper to determine whether the specified pdfannotator can be subscribed to.
     *
     * @param \stdClass $pdfannotator The record of the pdfannotator to test
     * @return bool
     */
    public static function is_subscribable($pdfannotator) {
        return (isloggedin() && !isguestuser() &&
                !self::is_forcesubscribed($pdfannotator) &&
                !self::subscription_disabled($pdfannotator));
    }

    /**
     * Set the pdfannotator subscription mode.
     *
     * By default when called without options, this is set to PDFANNOTATOR_FORCESUBSCRIBE.
     *
     * @param \stdClass $pdfannotatorid The id of the pdfannotator to set the state
     * @param int $status The new subscription state
     * @return bool
     * @throws \dml_exception
     */
    public static function set_subscription_mode($pdfannotatorid, $status = 1) {
        global $DB;
        return $DB->set_field("pdfannotator", "forcesubscribe", $status, array("id" => $pdfannotatorid));
    }

    /**
     * Returns the current subscription mode for the pdfannotator.
     *
     * @param \stdClass $pdfannotator The record of the pdfannotator to set
     * @return int The pdfannotator subscription mode
     */
    public static function get_subscription_mode($pdfannotator) {
        return $pdfannotator->forcesubscribe;
    }

    /**
     * Returns an array of pdfannotators that the current user is subscribed to and is allowed to unsubscribe from
     *
     * @return array An array of unsubscribable pdfannotators
     */
    public static function get_unsubscribable_pdfannotators() {
        global $USER, $DB;

        // Get courses that $USER is enrolled in and can see.
        $courses = enrol_get_my_courses();
        if (empty($courses)) {
            return array();
        }

        $courseids = array();
        foreach ($courses as $course) {
            $courseids[] = $course->id;
        }
        list($coursesql, $courseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'c');

        // Get all pdfannotators from the user's courses that they are subscribed to and which are not set to forced.
        // It is possible for users to be subscribed to a pdfannotator in subscription disallowed mode so they must be listed
        // here so that that can be unsubscribed from.
        $sql = "SELECT f.id, cm.id as cm, cm.visible, f.course
                FROM {pdfannotator} f
                JOIN {course_modules} cm ON cm.instance = f.id
                JOIN {modules} m ON m.name = :modulename AND m.id = cm.module
                LEFT JOIN {pdfannotator_subscriptions} fs ON (fs.pdfannotator = f.id AND fs.userid = :userid)
                WHERE f.forcesubscribe <> :forcesubscribe
                AND fs.id IS NOT NULL
                AND cm.course
                $coursesql";
        $params = array_merge($courseparams, array(
            'modulename' => 'pdfannotator',
            'userid' => $USER->id,
            'forcesubscribe' => pdfannotator_FORCESUBSCRIBE,
        ));
        $pdfannotators = $DB->get_recordset_sql($sql, $params);

        $unsubscribablepdfannotators = array();
        foreach ($pdfannotators as $pdfannotator) {
            if (empty($pdfannotator->visible)) {
                // The pdfannotator is hidden - check if the user can view the pdfannotator.
                $context = \context_module::instance($pdfannotator->cm);
                if (!has_capability('moodle/course:viewhiddenactivities', $context)) {
                    // The user can't see the hidden pdfannotator to cannot unsubscribe.
                    continue;
                }
            }

            $unsubscribablepdfannotators[] = $pdfannotator;
        }
        $pdfannotators->close();

        return $unsubscribablepdfannotators;
    }

    /**
     * Get the list of potential subscribers to a pdfannotator.
     *
     * @param context_module $context the pdfannotator context.
     * @param integer $groupid the id of a group, or 0 for all groups.
     * @param string $fields the list of fields to return for each user. As for get_users_by_capability.
     * @param string $sort sort order. As for get_users_by_capability.
     * @return array list of users.
     */
    public static function get_potential_subscribers($context, $groupid, $fields, $sort = '') {
        global $DB;

        // Only active enrolled users or everybody on the frontpage.
        list($esql, $params) = get_enrolled_sql($context, 'mod/pdfannotator:allowforcesubscribe', $groupid, true);
        if (!$sort) {
            list($sort, $sortparams) = users_order_by_sql('u');
            $params = array_merge($params, $sortparams);
        }

        $sql = "SELECT $fields
                FROM {user} u
                JOIN ($esql) je ON je.id = u.id
               WHERE u.auth <> 'nologin' AND u.suspended = 0 AND u.confirmed = 1
            ORDER BY $sort";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Fetch the pdfannotator subscription data for the specified userid and pdfannotator.
     *
     * @param int $pdfannotatorid The pdfannotator to retrieve a cache for
     * @param int $userid The user ID
     * @return boolean
     */
    public static function fetch_subscription_cache($pdfannotatorid, $userid) {
        if (isset(self::$pdfannotatorcache[$userid]) && isset(self::$pdfannotatorcache[$userid][$pdfannotatorid])) {
            return self::$pdfannotatorcache[$userid][$pdfannotatorid];
        }
        self::fill_subscription_cache($pdfannotatorid, $userid);

        if (!isset(self::$pdfannotatorcache[$userid]) || !isset(self::$pdfannotatorcache[$userid][$pdfannotatorid])) {
            return false;
        }

        return self::$pdfannotatorcache[$userid][$pdfannotatorid];
    }

    /**
     * Fill the pdfannotator subscription data for the specified userid and pdfannotator.
     *
     * If the userid is not specified, then all subscription data for that pdfannotator is fetched in a single query and used
     * for subsequent lookups without requiring further database queries.
     *
     * @param int $pdfannotatorid The pdfannotator to retrieve a cache for
     * @param int $userid The user ID
     * @return void
     */
    public static function fill_subscription_cache($pdfannotatorid, $userid = null) {
        global $DB;

        if (!isset(self::$fetchedpdfannotators[$pdfannotatorid])) {
            // This pdfannotator has not been fetched as a whole.
            if (isset($userid)) {
                if (!isset(self::$pdfannotatorcache[$userid])) {
                    self::$pdfannotatorcache[$userid] = array();
                }

                if (!isset(self::$pdfannotatorcache[$userid][$pdfannotatorid])) {
                    if ($DB->record_exists('pdfannotator_subscriptions', array(
                        'userid' => $userid,
                        'pdfannotator' => $pdfannotatorid,
                    ))) {
                        self::$pdfannotatorcache[$userid][$pdfannotatorid] = true;
                    } else {
                        self::$pdfannotatorcache[$userid][$pdfannotatorid] = false;
                    }
                }
            } else {
                $subscriptions = $DB->get_recordset('pdfannotator_subscriptions', array(
                    'pdfannotator' => $pdfannotatorid,
                ), '', 'id, userid');
                foreach ($subscriptions as $id => $data) {
                    if (!isset(self::$pdfannotatorcache[$data->userid])) {
                        self::$pdfannotatorcache[$data->userid] = array();
                    }
                    self::$pdfannotatorcache[$data->userid][$pdfannotatorid] = true;
                }
                self::$fetchedpdfannotators[$pdfannotatorid] = true;
                $subscriptions->close();
            }
        }
    }

    /**
     * Fill the pdfannotator subscription data for all pdfannotators that the specified userid can subscribe to in the specified
     * course.
     *
     * @param int $courseid The course to retrieve a cache for
     * @param int $userid The user ID
     * @return void
     */
    public static function fill_subscription_cache_for_course($courseid, $userid) {
        global $DB;

        if (!isset(self::$pdfannotatorcache[$userid])) {
            self::$pdfannotatorcache[$userid] = array();
        }

        $sql = "SELECT
                    f.id AS pdfannotatorid,
                    s.id AS subscriptionid
                FROM {pdfannotator} f
                LEFT JOIN {pdfannotator_subscriptions} s ON (s.pdfannotator = f.id AND s.userid = :userid)
                WHERE f.course = :course
                AND f.forcesubscribe <> :subscriptionforced";

        $subscriptions = $DB->get_recordset_sql($sql, array(
            'course' => $courseid,
            'userid' => $userid,
            'subscriptionforced' => pdfannotator_FORCESUBSCRIBE,
        ));

        foreach ($subscriptions as $id => $data) {
            self::$pdfannotatorcache[$userid][$id] = !empty($data->subscriptionid);
        }
        $subscriptions->close();
    }

    /**
     * Returns a list of user objects who are subscribed to this pdfannotator.
     *
     * @param stdClass $pdfannotator The pdfannotator record.
     * @param int $groupid The group id if restricting subscriptions to a group of users, or 0 for all.
     * @param context_module $context the pdfannotator context, to save re-fetching it where possible.
     * @param string $fields requested user fields (with "u." table prefix).
     * @param boolean $includediscussionsubscriptions Whether to take discussion subscriptions and unsubscriptions into
     * consideration.
     * @return array list of users.
     */
    public static function fetch_subscribed_users($pdfannotator, $groupid = 0, $context = null, $fields = null,
            $includediscussionsubscriptions = false) {
        global $CFG, $DB;

        if (empty($fields)) {
            $userfieldsapi = \core_user\fields::for_name();
            $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
            $fields = "u.id,
                       u.username,
                       $allnames,
                       u.maildisplay,
                       u.mailformat,
                       u.maildigest,
                       u.imagealt,
                       u.email,
                       u.emailstop,
                       u.city,
                       u.country,
                       u.lastaccess,
                       u.lastlogin,
                       u.picture,
                       u.timezone,
                       u.theme,
                       u.lang,
                       u.trackpdfannotators,
                       u.mnethostid";
        }

        // Retrieve the pdfannotator context if it wasn't specified.
        $context = pdfannotator_get_context($pdfannotator->id, $context);

        if (self::is_forcesubscribed($pdfannotator)) {
            $results = self::get_potential_subscribers($context, $groupid, $fields, "u.email ASC");

        } else {
            // Only active enrolled users or everybody on the frontpage.
            list($esql, $params) = get_enrolled_sql($context, '', $groupid, true);
            $params['pdfannotatorid'] = $pdfannotator->id;

            if ($includediscussionsubscriptions) {
                $params['spdfannotatorid'] = $pdfannotator->id;
                $params['dspdfannotatorid'] = $pdfannotator->id;
                $params['unsubscribed'] = self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED;

                $sql = "SELECT $fields
                        FROM (
                            SELECT userid FROM {pdfannotator_subscriptions} s
                            WHERE
                                s.pdfannotator = :spdfannotatorid
                                UNION
                            SELECT userid FROM {pdfannotator_discussion_subs} ds
                            WHERE
                                ds.pdfannotator = :dspdfannotatorid AND ds.preference <> :unsubscribed
                        ) subscriptions
                        JOIN {user} u ON u.id = subscriptions.userid
                        JOIN ($esql) je ON je.id = u.id
                        WHERE u.auth <> 'nologin' AND u.suspended = 0 AND u.confirmed = 1
                        ORDER BY u.email ASC";

            } else {
                $sql = "SELECT $fields
                        FROM {user} u
                        JOIN ($esql) je ON je.id = u.id
                        JOIN {pdfannotator_subscriptions} s ON s.userid = u.id
                        WHERE
                          s.pdfannotator = :pdfannotatorid AND u.auth <> 'nologin' AND u.suspended = 0 AND u.confirmed = 1
                        ORDER BY u.email ASC";
            }
            $results = $DB->get_records_sql($sql, $params);
        }

        // Guest user should never be subscribed to a pdfannotator.
        unset($results[$CFG->siteguest]);

        // Apply the activity module availability resetrictions.
        $cm = get_coursemodule_from_instance('pdfannotator', $pdfannotator->id, $pdfannotator->course);
        $modinfo = get_fast_modinfo($pdfannotator->course);
        $info = new \core_availability\info_module($modinfo->get_cm($cm->id));
        $results = $info->filter_user_list($results);

        return $results;
    }

    /**
     * Retrieve the discussion subscription data for the specified userid and pdfannotator.
     *
     * This is returned as an array of discussions for that pdfannotator which contain the preference in a stdClass.
     *
     * @param int $pdfannotatorid The pdfannotator to retrieve a cache for
     * @param int $userid The user ID
     * @return array of stdClass objects with one per discussion in the pdfannotator.
     */
    public static function fetch_discussion_subscription($pdfannotatorid, $userid = null) {
        self::fill_discussion_subscription_cache($pdfannotatorid, $userid);

        if (!isset(self::$pdfannotatordiscussioncache[$userid]) ||
            !isset(self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid])) {
            return array();
        }

        return self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid];
    }

    /**
     * Fill the discussion subscription data for the specified userid and pdfannotator.
     *
     * If the userid is not specified, then all discussion subscription data for that pdfannotator is fetched in a single query
     * and used for subsequent lookups without requiring further database queries.
     *
     * @param int $pdfannotatorid The pdfannotator to retrieve a cache for
     * @param int $userid The user ID
     * @return void
     */
    public static function fill_discussion_subscription_cache($pdfannotatorid, $userid = null) {
        global $DB;

        if (!isset(self::$discussionfetchedpdfannotators[$pdfannotatorid])) {
            // This pdfannotator hasn't been fetched as a whole yet.
            if (isset($userid)) {
                if (!isset(self::$pdfannotatordiscussioncache[$userid])) {
                    self::$pdfannotatordiscussioncache[$userid] = array();
                }

                if (!isset(self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid])) {
                    $subscriptions = $DB->get_recordset('pdfannotator_discussion_subs', array(
                        'userid' => $userid,
                        'pdfannotator' => $pdfannotatorid,
                    ), null, 'id, discussion, preference');

                    self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid] = array();
                    foreach ($subscriptions as $id => $data) {
                        self::add_to_discussion_cache($pdfannotatorid, $userid, $data->discussion, $data->preference);
                    }

                    $subscriptions->close();
                }
            } else {
                $subscriptions = $DB->get_recordset('pdfannotator_discussion_subs', array(
                    'pdfannotator' => $pdfannotatorid,
                ), null, 'id, userid, discussion, preference');
                foreach ($subscriptions as $id => $data) {
                    self::add_to_discussion_cache($pdfannotatorid, $data->userid, $data->discussion, $data->preference);
                }
                self::$discussionfetchedpdfannotators[$pdfannotatorid] = true;
                $subscriptions->close();
            }
        }
    }

    /**
     * Add the specified discussion and user preference to the discussion
     * subscription cache.
     *
     * @param int $pdfannotatorid The ID of the pdfannotator that this preference belongs to
     * @param int $userid The ID of the user that this preference belongs to
     * @param int $discussion The ID of the discussion that this preference relates to
     * @param int $preference The preference to store
     */
    protected static function add_to_discussion_cache($pdfannotatorid, $userid, $discussion, $preference) {
        if (!isset(self::$pdfannotatordiscussioncache[$userid])) {
            self::$pdfannotatordiscussioncache[$userid] = array();
        }

        if (!isset(self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid])) {
            self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid] = array();
        }

        self::$pdfannotatordiscussioncache[$userid][$pdfannotatorid][$discussion] = $preference;
    }

    /**
     * Reset the discussion cache.
     *
     * This cache is used to reduce the number of database queries when
     * checking pdfannotator discussion subscription states.
     */
    public static function reset_discussion_cache() {
        self::$pdfannotatordiscussioncache = array();
        self::$discussionfetchedpdfannotators = array();
    }

    /**
     * Reset the pdfannotator cache.
     *
     * This cache is used to reduce the number of database queries when
     * checking pdfannotator subscription states.
     */
    public static function reset_pdfannotator_cache() {
        self::$pdfannotatorcache = array();
        self::$fetchedpdfannotators = array();
    }

    /**
     * Adds user to the subscriber list.
     *
     * @param int $userid The ID of the user to subscribe
     * @param \stdClass $pdfannotator The pdfannotator record for this pdfannotator.
     * @param \context_module|null $context Module context, may be omitted if not known or if called for the current
     *      module set in page.
     * @param boolean $userrequest Whether the user requested this change themselves. This has an effect on whether
     *     discussion subscriptions are removed too.
     * @return bool|int Returns true if the user is already subscribed, or the pdfannotator_subscriptions ID if the user was
     *     successfully subscribed.
     */
    public static function subscribe_user($userid, $pdfannotator, $context = null, $userrequest = false) {
        global $DB;

        if (self::is_subscribed($userid, $pdfannotator)) {
            return true;
        }

        $sub = new \stdClass();
        $sub->userid  = $userid;
        $sub->pdfannotator = $pdfannotator->id;

        $result = $DB->insert_record("pdfannotator_subscriptions", $sub);

        if ($userrequest) {
            $discussionsubscriptions = $DB->get_recordset('pdfannotator_discussion_subs', array('userid' => $userid,
                'pdfannotator' => $pdfannotator->id));
            $DB->delete_records_select('pdfannotator_discussion_subs',
                    'userid = :userid AND pdfannotator = :pdfannotatorid AND preference <> :preference', array(
                        'userid' => $userid,
                        'pdfannotatorid' => $pdfannotator->id,
                        'preference' => self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED,
                    ));

            // Reset the subscription caches for this pdfannotator.
            // We know that the there were previously entries and there aren't any more.
            if (isset(self::$pdfannotatordiscussioncache[$userid]) &&
                isset(self::$pdfannotatordiscussioncache[$userid][$pdfannotator->id])) {
                foreach (self::$pdfannotatordiscussioncache[$userid][$pdfannotator->id] as $discussionid => $preference) {
                    if ($preference != self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED) {
                        unset(self::$pdfannotatordiscussioncache[$userid][$pdfannotator->id][$discussionid]);
                    }
                }
            }
        }

        // Reset the cache for this pdfannotator.
        self::$pdfannotatorcache[$userid][$pdfannotator->id] = true;

        $context = pdfannotator_get_context($pdfannotator->id, $context);
        $params = array(
            'context' => $context,
            'objectid' => $result,
            'relateduserid' => $userid,
            'other' => array('pdfannotatorid' => $pdfannotator->id),

        );
        $event  = event\subscription_created::create($params);
        if ($userrequest && $discussionsubscriptions) {
            foreach ($discussionsubscriptions as $subscription) {
                $event->add_record_snapshot('pdfannotator_discussion_subs', $subscription);
            }
            $discussionsubscriptions->close();
        }
        $event->trigger();

        return $result;
    }

    /**
     * Removes user from the subscriber list
     *
     * @param int $userid The ID of the user to unsubscribe
     * @param \stdClass $pdfannotator The pdfannotator record for this pdfannotator.
     * @param \context_module|null $context Module context, may be omitted if not known or if called for the current
     *     module set in page.
     * @param boolean $userrequest Whether the user requested this change themselves. This has an effect on whether
     *     discussion subscriptions are removed too.
     * @return boolean Always returns true.
     */
    public static function unsubscribe_user($userid, $pdfannotator, $context = null, $userrequest = false) {
        global $DB;

        $sqlparams = array(
            'userid' => $userid,
            'pdfannotator' => $pdfannotator->id,
        );
        $DB->delete_records('pdfannotator_digests', $sqlparams);

        if ($pdfannotatorsubscription = $DB->get_record('pdfannotator_subscriptions', $sqlparams)) {
            $DB->delete_records('pdfannotator_subscriptions', array('id' => $pdfannotatorsubscription->id));

            if ($userrequest) {
                $discussionsubscriptions = $DB->get_recordset('pdfannotator_discussion_subs', $sqlparams);
                $DB->delete_records('pdfannotator_discussion_subs',
                        array('userid' => $userid, 'pdfannotator' => $pdfannotator->id,
                            'preference' => self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED));

                // We know that the there were previously entries and there aren't any more.
                if (isset(self::$pdfannotatordiscussioncache[$userid]) &&
                    isset(self::$pdfannotatordiscussioncache[$userid][$pdfannotator->id])) {
                    self::$pdfannotatordiscussioncache[$userid][$pdfannotator->id] = array();
                }
            }

            // Reset the cache for this pdfannotator.
            self::$pdfannotatorcache[$userid][$pdfannotator->id] = false;

            $context = pdfannotator_get_context($pdfannotator->id, $context);
            $params = array(
                'context' => $context,
                'objectid' => $pdfannotatorsubscription->id,
                'relateduserid' => $userid,
                'other' => array('pdfannotatorid' => $pdfannotator->id),

            );
            $event = event\subscription_deleted::create($params);
            $event->add_record_snapshot('pdfannotator_subscriptions', $pdfannotatorsubscription);
            if ($userrequest && $discussionsubscriptions) {
                foreach ($discussionsubscriptions as $subscription) {
                    $event->add_record_snapshot('pdfannotator_discussion_subs', $subscription);
                }
                $discussionsubscriptions->close();
            }
            $event->trigger();
        }

        return true;
    }

    /**
     * Subscribes the user to the specified discussion.
     *
     * @param int $userid The userid of the user being subscribed
     * @param \stdClass $discussion The discussion to subscribe to
     * @param \context_module|null $context Module context, may be omitted if not known or if called for the current
     *     module set in page.
     * @return boolean Whether a change was made
     */
    public static function subscribe_user_to_discussion($userid, $discussion, $context = null) {
        global $DB;

        // First check whether the user is subscribed to the discussion already.
        $subscription = $DB->get_record('pdfannotator_discussion_subs', array('userid' => $userid,
            'discussion' => $discussion->id));
        if ($subscription) {
            if ($subscription->preference != self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED) {
                // The user is already subscribed to the discussion. Ignore.
                return false;
            }
        }
        // No discussion-level subscription. Check for a pdfannotator level subscription.
        if ($DB->record_exists('pdfannotator_subscriptions', array('userid' => $userid,
            'pdfannotator' => $discussion->pdfannotator))) {
            if ($subscription && $subscription->preference == self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED) {
                // The user is subscribed to the pdfannotator, but unsubscribed from the discussion, delete the discussion
                // preference.
                $DB->delete_records('pdfannotator_discussion_subs', array('id' => $subscription->id));
                unset(self::$pdfannotatordiscussioncache[$userid][$discussion->pdfannotator][$discussion->id]);
            } else {
                // The user is already subscribed to the pdfannotator. Ignore.
                return false;
            }
        } else {
            if ($subscription) {
                $subscription->preference = time();
                $DB->update_record('pdfannotator_discussion_subs', $subscription);
            } else {
                $subscription = new \stdClass();
                $subscription->userid  = $userid;
                $subscription->pdfannotator = $discussion->pdfannotator;
                $subscription->discussion = $discussion->id;
                $subscription->preference = time();

                $subscription->id = $DB->insert_record('pdfannotator_discussion_subs', $subscription);
                self::$pdfannotatordiscussioncache[$userid][$discussion->pdfannotator][$discussion->id] = $subscription->preference;
            }
        }

        $context = pdfannotator_get_context($discussion->pdfannotator, $context);
        $params = array(
            'context' => $context,
            'objectid' => $subscription->id,
            'relateduserid' => $userid,
            'other' => array(
                'pdfannotatorid' => $discussion->pdfannotator,
                'discussion' => $discussion->id,
            ),

        );
        $event  = event\discussion_subscription_created::create($params);
        $event->trigger();

        return true;
    }
    /**
     * Unsubscribes the user from the specified discussion.
     *
     * @param int $userid The userid of the user being unsubscribed
     * @param \stdClass $discussion The discussion to unsubscribe from
     * @param \context_module|null $context Module context, may be omitted if not known or if called for the current
     *     module set in page.
     * @return boolean Whether a change was made
     */
    public static function unsubscribe_user_from_discussion($userid, $discussion, $context = null) {
        global $DB;

        // First check whether the user's subscription preference for this discussion.
        $subscription = $DB->get_record('pdfannotator_discussion_subs', array('userid' => $userid,
            'discussion' => $discussion->id));
        if ($subscription) {
            if ($subscription->preference == self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED) {
                // The user is already unsubscribed from the discussion. Ignore.
                return false;
            }
        }
        // No discussion-level preference. Check for a pdfannotator level subscription.
        if (!$DB->record_exists('pdfannotator_subscriptions', array('userid' => $userid,
            'pdfannotator' => $discussion->pdfannotator))) {
            if ($subscription && $subscription->preference != self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED) {
                // The user is not subscribed to the pdfannotator, but subscribed from the discussion, delete the discussion
                // subscription.
                $DB->delete_records('pdfannotator_discussion_subs', array('id' => $subscription->id));
                unset(self::$pdfannotatordiscussioncache[$userid][$discussion->pdfannotator][$discussion->id]);
            } else {
                // The user is not subscribed from the pdfannotator. Ignore.
                return false;
            }
        } else {
            if ($subscription) {
                $subscription->preference = self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED;
                $DB->update_record('pdfannotator_discussion_subs', $subscription);
            } else {
                $subscription = new \stdClass();
                $subscription->userid  = $userid;
                $subscription->pdfannotator = $discussion->pdfannotator;
                $subscription->discussion = $discussion->id;
                $subscription->preference = self::PDFANNOTATOR_DISCUSSION_UNSUBSCRIBED;

                $subscription->id = $DB->insert_record('pdfannotator_discussion_subs', $subscription);
            }
            self::$pdfannotatordiscussioncache[$userid][$discussion->pdfannotator][$discussion->id] = $subscription->preference;
        }

        $context = pdfannotator_get_context($discussion->pdfannotator, $context);
        $params = array(
            'context' => $context,
            'objectid' => $subscription->id,
            'relateduserid' => $userid,
            'other' => array(
                'pdfannotatorid' => $discussion->pdfannotator,
                'discussion' => $discussion->id,
            ),

        );
        $event  = event\discussion_subscription_deleted::create($params);
        $event->trigger();

        return true;
    }

    /**
     * Gets the default subscription value for the logged in user.
     *
     * @param \stdClass $pdfannotator The pdfannotator record
     * @param \context $context The course context
     * @param \cm_info $cm cm_info
     * @param int|null $discussionid The discussion we are checking against
     * @return bool Default subscription
     * @throws coding_exception
     */
    public static function get_user_default_subscription($pdfannotator, $context, $cm, ?int $discussionid) {
        global $USER;
        $manageactivities = has_capability('moodle/course:manageactivities', $context);
        if (self::subscription_disabled($pdfannotator) && !$manageactivities) {
            // User does not have permission to subscribe to this discussion at all.
            $discussionsubscribe = false;
        } else if (self::is_forcesubscribed($pdfannotator)) {
            // User does not have permission to unsubscribe from this discussion at all.
            $discussionsubscribe = true;
        } else {
            if (isset($discussionid) && self::is_subscribed($USER->id, $pdfannotator, $discussionid, $cm)) {
                // User is subscribed to the discussion - continue the subscription.
                $discussionsubscribe = true;
            } else if (!isset($discussionid) && self::is_subscribed($USER->id, $pdfannotator, null, $cm)) {
                // Starting a new discussion, and the user is subscribed to the pdfannotator - subscribe to the discussion.
                $discussionsubscribe = true;
            } else {
                // User is not subscribed to either pdfannotator or discussion. Follow user preference.
                $discussionsubscribe = $USER->autosubscribe ?? false;
            }
        }

        return $discussionsubscribe;
    }
}

