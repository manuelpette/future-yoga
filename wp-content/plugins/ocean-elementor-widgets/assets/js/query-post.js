(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

/**
 * Because Elementor plugin uses jQuery for controls,
 * We also have to use jQuery to create new one
 */
jQuery(document).ready(function () {
  jQuery(window).on("elementor/init elementor:init", function () {
    var ControlQueryPostSearch = elementor.modules.controls.BaseData.extend({
      isPostSearchReady: false,
      getPostTitlesbyID: function getPostTitlesbyID() {
        var self = this;
        var postIDs = this.getControlValue();

        if (!postIDs) {
          return;
        }

        if (!_.isArray(postIDs)) {
          postIDs = [postIDs];
        }

        self.addControlSpinner();
        /**
         * Because Elementor plugin uses jQuery for controls,
         * We also have to use jQuery to create new one
         */

        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            action: "oew_get_posts_title_by_id",
            nonce: queryPostData.nonce,
            id: postIDs
          },
          success: function success(results) {
            self.isPostSearchReady = true;
            self.model.set("options", results);
            self.render();
          }
        });
      },
      addControlSpinner: function addControlSpinner() {
        this.ui.select.prop("disabled", true);
        this.$el.find(".elementor-control-title").after('<span class="elementor-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
      },
      onReady: function onReady() {
        var self = this;
        this.ui.select.select2({
          placeholder: "Search",
          allowClear: true,
          minimumInputLength: 2,
          ajax: {
            url: ajaxurl,
            dataType: "json",
            method: "post",
            delay: 250,
            data: function data(params) {
              return {
                action: "oew_get_posts_by_query",
                nonce: queryPostData.nonce,
                q: params.term,
                // search term
                post_type: self.model.get("post_type")
              };
            },
            processResults: function processResults(data) {
              return {
                results: data
              };
            },
            cache: true
          }
        });

        if (!this.isPostSearchReady) {
          this.getPostTitlesbyID();
        }
      },
      onBeforeDestroy: function onBeforeDestroy() {
        if (this.ui.select.data("select2")) {
          this.ui.select.select2("destroy");
        }

        this.$el.remove();
      }
    });
    elementor.addControlView("oew-query-posts", ControlQueryPostSearch);
  });
});

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvc3JjL2pzL2NvbnRyb2xzL3F1ZXJ5LXBvc3QuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsTUFBTSxDQUFDLFFBQUQsQ0FBTixDQUFpQixLQUFqQixDQUF1QixZQUFNO0FBQ3pCLEVBQUEsTUFBTSxDQUFDLE1BQUQsQ0FBTixDQUFlLEVBQWYsQ0FBa0IsK0JBQWxCLEVBQW1ELFlBQVk7QUFDM0QsUUFBTSxzQkFBc0IsR0FBRyxTQUFTLENBQUMsT0FBVixDQUFrQixRQUFsQixDQUEyQixRQUEzQixDQUFvQyxNQUFwQyxDQUEyQztBQUN0RSxNQUFBLGlCQUFpQixFQUFFLEtBRG1EO0FBR3RFLE1BQUEsaUJBQWlCLEVBQUUsNkJBQVk7QUFDM0IsWUFBTSxJQUFJLEdBQUcsSUFBYjtBQUNBLFlBQUksT0FBTyxHQUFHLEtBQUssZUFBTCxFQUFkOztBQUVBLFlBQUksQ0FBQyxPQUFMLEVBQWM7QUFDVjtBQUNIOztBQUVELFlBQUksQ0FBQyxDQUFDLENBQUMsT0FBRixDQUFVLE9BQVYsQ0FBTCxFQUF5QjtBQUNyQixVQUFBLE9BQU8sR0FBRyxDQUFDLE9BQUQsQ0FBVjtBQUNIOztBQUVELFFBQUEsSUFBSSxDQUFDLGlCQUFMO0FBRUE7QUFDaEI7QUFDQTtBQUNBOztBQUNnQixRQUFBLE1BQU0sQ0FBQyxJQUFQLENBQVk7QUFDUixVQUFBLEdBQUcsRUFBRSxPQURHO0FBRVIsVUFBQSxJQUFJLEVBQUUsTUFGRTtBQUdSLFVBQUEsSUFBSSxFQUFFO0FBQ0YsWUFBQSxNQUFNLEVBQUUsMkJBRE47QUFFRixZQUFBLEtBQUssRUFBRSxhQUFhLENBQUMsS0FGbkI7QUFHRixZQUFBLEVBQUUsRUFBRTtBQUhGLFdBSEU7QUFRUixVQUFBLE9BQU8sRUFBRSxpQkFBVSxPQUFWLEVBQW1CO0FBQ3hCLFlBQUEsSUFBSSxDQUFDLGlCQUFMLEdBQXlCLElBQXpCO0FBQ0EsWUFBQSxJQUFJLENBQUMsS0FBTCxDQUFXLEdBQVgsQ0FBZSxTQUFmLEVBQTBCLE9BQTFCO0FBQ0EsWUFBQSxJQUFJLENBQUMsTUFBTDtBQUNIO0FBWk8sU0FBWjtBQWNILE9BbkNxRTtBQW9DdEUsTUFBQSxpQkFBaUIsRUFBRSw2QkFBWTtBQUMzQixhQUFLLEVBQUwsQ0FBUSxNQUFSLENBQWUsSUFBZixDQUFvQixVQUFwQixFQUFnQyxJQUFoQztBQUNBLGFBQUssR0FBTCxDQUNLLElBREwsQ0FDVSwwQkFEVixFQUVLLEtBRkwsQ0FHUSxrR0FIUjtBQUtILE9BM0NxRTtBQTRDdEUsTUFBQSxPQUFPLEVBQUUsbUJBQVk7QUFDakIsWUFBSSxJQUFJLEdBQUcsSUFBWDtBQUVBLGFBQUssRUFBTCxDQUFRLE1BQVIsQ0FBZSxPQUFmLENBQXVCO0FBQ25CLFVBQUEsV0FBVyxFQUFFLFFBRE07QUFFbkIsVUFBQSxVQUFVLEVBQUUsSUFGTztBQUduQixVQUFBLGtCQUFrQixFQUFFLENBSEQ7QUFLbkIsVUFBQSxJQUFJLEVBQUU7QUFDRixZQUFBLEdBQUcsRUFBRSxPQURIO0FBRUYsWUFBQSxRQUFRLEVBQUUsTUFGUjtBQUdGLFlBQUEsTUFBTSxFQUFFLE1BSE47QUFJRixZQUFBLEtBQUssRUFBRSxHQUpMO0FBS0YsWUFBQSxJQUFJLEVBQUUsY0FBVSxNQUFWLEVBQWtCO0FBQ3BCLHFCQUFPO0FBQ0gsZ0JBQUEsTUFBTSxFQUFFLHdCQURMO0FBRUgsZ0JBQUEsS0FBSyxFQUFFLGFBQWEsQ0FBQyxLQUZsQjtBQUdILGdCQUFBLENBQUMsRUFBRSxNQUFNLENBQUMsSUFIUDtBQUdhO0FBQ2hCLGdCQUFBLFNBQVMsRUFBRSxJQUFJLENBQUMsS0FBTCxDQUFXLEdBQVgsQ0FBZSxXQUFmO0FBSlIsZUFBUDtBQU1ILGFBWkM7QUFhRixZQUFBLGNBQWMsRUFBRSx3QkFBVSxJQUFWLEVBQWdCO0FBQzVCLHFCQUFPO0FBQ0gsZ0JBQUEsT0FBTyxFQUFFO0FBRE4sZUFBUDtBQUdILGFBakJDO0FBa0JGLFlBQUEsS0FBSyxFQUFFO0FBbEJMO0FBTGEsU0FBdkI7O0FBMkJBLFlBQUksQ0FBQyxLQUFLLGlCQUFWLEVBQTZCO0FBQ3pCLGVBQUssaUJBQUw7QUFDSDtBQUNKLE9BN0VxRTtBQThFdEUsTUFBQSxlQUFlLEVBQUUsMkJBQVk7QUFDekIsWUFBSSxLQUFLLEVBQUwsQ0FBUSxNQUFSLENBQWUsSUFBZixDQUFvQixTQUFwQixDQUFKLEVBQW9DO0FBQ2hDLGVBQUssRUFBTCxDQUFRLE1BQVIsQ0FBZSxPQUFmLENBQXVCLFNBQXZCO0FBQ0g7O0FBRUQsYUFBSyxHQUFMLENBQVMsTUFBVDtBQUNIO0FBcEZxRSxLQUEzQyxDQUEvQjtBQXVGQSxJQUFBLFNBQVMsQ0FBQyxjQUFWLENBQXlCLGlCQUF6QixFQUE0QyxzQkFBNUM7QUFDSCxHQXpGRDtBQTBGSCxDQTNGRCIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gcihlLG4sdCl7ZnVuY3Rpb24gbyhpLGYpe2lmKCFuW2ldKXtpZighZVtpXSl7dmFyIGM9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZTtpZighZiYmYylyZXR1cm4gYyhpLCEwKTtpZih1KXJldHVybiB1KGksITApO3ZhciBhPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIraStcIidcIik7dGhyb3cgYS5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGF9dmFyIHA9bltpXT17ZXhwb3J0czp7fX07ZVtpXVswXS5jYWxsKHAuZXhwb3J0cyxmdW5jdGlvbihyKXt2YXIgbj1lW2ldWzFdW3JdO3JldHVybiBvKG58fHIpfSxwLHAuZXhwb3J0cyxyLGUsbix0KX1yZXR1cm4gbltpXS5leHBvcnRzfWZvcih2YXIgdT1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlLGk9MDtpPHQubGVuZ3RoO2krKylvKHRbaV0pO3JldHVybiBvfXJldHVybiByfSkoKSIsIi8qKlxyXG4gKiBCZWNhdXNlIEVsZW1lbnRvciBwbHVnaW4gdXNlcyBqUXVlcnkgZm9yIGNvbnRyb2xzLFxyXG4gKiBXZSBhbHNvIGhhdmUgdG8gdXNlIGpRdWVyeSB0byBjcmVhdGUgbmV3IG9uZVxyXG4gKi9cclxualF1ZXJ5KGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XHJcbiAgICBqUXVlcnkod2luZG93KS5vbihcImVsZW1lbnRvci9pbml0IGVsZW1lbnRvcjppbml0XCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBjb25zdCBDb250cm9sUXVlcnlQb3N0U2VhcmNoID0gZWxlbWVudG9yLm1vZHVsZXMuY29udHJvbHMuQmFzZURhdGEuZXh0ZW5kKHtcclxuICAgICAgICAgICAgaXNQb3N0U2VhcmNoUmVhZHk6IGZhbHNlLFxyXG5cclxuICAgICAgICAgICAgZ2V0UG9zdFRpdGxlc2J5SUQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xyXG4gICAgICAgICAgICAgICAgbGV0IHBvc3RJRHMgPSB0aGlzLmdldENvbnRyb2xWYWx1ZSgpO1xyXG5cclxuICAgICAgICAgICAgICAgIGlmICghcG9zdElEcykge1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICBpZiAoIV8uaXNBcnJheShwb3N0SURzKSkge1xyXG4gICAgICAgICAgICAgICAgICAgIHBvc3RJRHMgPSBbcG9zdElEc107XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgc2VsZi5hZGRDb250cm9sU3Bpbm5lcigpO1xyXG5cclxuICAgICAgICAgICAgICAgIC8qKlxyXG4gICAgICAgICAgICAgICAgICogQmVjYXVzZSBFbGVtZW50b3IgcGx1Z2luIHVzZXMgalF1ZXJ5IGZvciBjb250cm9scyxcclxuICAgICAgICAgICAgICAgICAqIFdlIGFsc28gaGF2ZSB0byB1c2UgalF1ZXJ5IHRvIGNyZWF0ZSBuZXcgb25lXHJcbiAgICAgICAgICAgICAgICAgKi9cclxuICAgICAgICAgICAgICAgIGpRdWVyeS5hamF4KHtcclxuICAgICAgICAgICAgICAgICAgICB1cmw6IGFqYXh1cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBhY3Rpb246IFwib2V3X2dldF9wb3N0c190aXRsZV9ieV9pZFwiLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBub25jZTogcXVlcnlQb3N0RGF0YS5ub25jZSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWQ6IHBvc3RJRHMsXHJcbiAgICAgICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzdWx0cykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmlzUG9zdFNlYXJjaFJlYWR5ID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5tb2RlbC5zZXQoXCJvcHRpb25zXCIsIHJlc3VsdHMpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLnJlbmRlcigpO1xyXG4gICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgYWRkQ29udHJvbFNwaW5uZXI6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMudWkuc2VsZWN0LnByb3AoXCJkaXNhYmxlZFwiLCB0cnVlKTtcclxuICAgICAgICAgICAgICAgIHRoaXMuJGVsXHJcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoXCIuZWxlbWVudG9yLWNvbnRyb2wtdGl0bGVcIilcclxuICAgICAgICAgICAgICAgICAgICAuYWZ0ZXIoXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICc8c3BhbiBjbGFzcz1cImVsZW1lbnRvci1jb250cm9sLXNwaW5uZXJcIj4mbmJzcDs8aSBjbGFzcz1cImZhIGZhLXNwaW5uZXIgZmEtc3BpblwiPjwvaT4mbmJzcDs8L3NwYW4+J1xyXG4gICAgICAgICAgICAgICAgICAgICk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIG9uUmVhZHk6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuXHJcbiAgICAgICAgICAgICAgICB0aGlzLnVpLnNlbGVjdC5zZWxlY3QyKHtcclxuICAgICAgICAgICAgICAgICAgICBwbGFjZWhvbGRlcjogXCJTZWFyY2hcIixcclxuICAgICAgICAgICAgICAgICAgICBhbGxvd0NsZWFyOiB0cnVlLFxyXG4gICAgICAgICAgICAgICAgICAgIG1pbmltdW1JbnB1dExlbmd0aDogMixcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgYWpheDoge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB1cmw6IGFqYXh1cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiBcImpzb25cIixcclxuICAgICAgICAgICAgICAgICAgICAgICAgbWV0aG9kOiBcInBvc3RcIixcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGVsYXk6IDI1MCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YTogZnVuY3Rpb24gKHBhcmFtcykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBhY3Rpb246IFwib2V3X2dldF9wb3N0c19ieV9xdWVyeVwiLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5vbmNlOiBxdWVyeVBvc3REYXRhLm5vbmNlLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHE6IHBhcmFtcy50ZXJtLCAvLyBzZWFyY2ggdGVybVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBvc3RfdHlwZTogc2VsZi5tb2RlbC5nZXQoXCJwb3N0X3R5cGVcIiksXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9O1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBwcm9jZXNzUmVzdWx0czogZnVuY3Rpb24gKGRhdGEpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0czogZGF0YSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhY2hlOiB0cnVlLFxyXG4gICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgICAgICBpZiAoIXRoaXMuaXNQb3N0U2VhcmNoUmVhZHkpIHtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLmdldFBvc3RUaXRsZXNieUlEKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIG9uQmVmb3JlRGVzdHJveTogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMudWkuc2VsZWN0LmRhdGEoXCJzZWxlY3QyXCIpKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy51aS5zZWxlY3Quc2VsZWN0MihcImRlc3Ryb3lcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgdGhpcy4kZWwucmVtb3ZlKCk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIGVsZW1lbnRvci5hZGRDb250cm9sVmlldyhcIm9ldy1xdWVyeS1wb3N0c1wiLCBDb250cm9sUXVlcnlQb3N0U2VhcmNoKTtcclxuICAgIH0pO1xyXG59KTtcclxuIl19
