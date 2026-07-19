(function (wp) {
  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;
  var TextControl = wp.components.TextControl;
  var Placeholder = wp.components.Placeholder;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var __ = wp.i18n.__;

  var widgetLabels = {
    ticker: __("Fuel Price Ticker", "oilpriceapi-widgets"),
    diesel: __("U.S. Diesel Price", "oilpriceapi-widgets"),
    "fuel-surcharge": __("Fuel Surcharge Calculator", "oilpriceapi-widgets"),
  };

  registerBlockType("oilpriceapi/widget", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var blockProps = useBlockProps();

      var widgetType = attributes.widgetType || "ticker";
      var theme = attributes.theme || "dark";

      var inspectorControls = el(
        InspectorControls,
        null,
        el(
          PanelBody,
          {
            title: __("Widget Settings", "oilpriceapi-widgets"),
            initialOpen: true,
          },
          el(SelectControl, {
            label: __("Widget Type", "oilpriceapi-widgets"),
            value: widgetType,
            options: [
              {
                label: __("Fuel Price Ticker", "oilpriceapi-widgets"),
                value: "ticker",
              },
              {
                label: __("U.S. Diesel Price", "oilpriceapi-widgets"),
                value: "diesel",
              },
              {
                label: __("Fuel Surcharge Calculator", "oilpriceapi-widgets"),
                value: "fuel-surcharge",
              },
            ],
            onChange: function (value) {
              setAttributes({ widgetType: value });
            },
          }),
          el(SelectControl, {
            label: __("Theme", "oilpriceapi-widgets"),
            value: theme,
            options: [
              { label: __("Dark", "oilpriceapi-widgets"), value: "dark" },
              { label: __("Light", "oilpriceapi-widgets"), value: "light" },
            ],
            onChange: function (value) {
              setAttributes({ theme: value });
            },
          }),

          // Ticker-specific options.
          widgetType === "ticker" &&
            el(SelectControl, {
              label: __("Layout", "oilpriceapi-widgets"),
              value: attributes.layout || "horizontal",
              options: [
                {
                  label: __("Horizontal", "oilpriceapi-widgets"),
                  value: "horizontal",
                },
                {
                  label: __("Vertical", "oilpriceapi-widgets"),
                  value: "vertical",
                },
              ],
              onChange: function (value) {
                setAttributes({ layout: value });
              },
            }),
          widgetType === "ticker" &&
            el(TextControl, {
              label: __("Fuels", "oilpriceapi-widgets"),
              help: __(
                "Comma-separated: diesel, gasoline",
                "oilpriceapi-widgets",
              ),
              value: attributes.fuels || "diesel,gasoline",
              onChange: function (value) {
                setAttributes({ fuels: value });
              },
            }),

          // Fuel surcharge-specific options.
          widgetType === "fuel-surcharge" &&
            el(TextControl, {
              label: __("Base Fuel Price ($/gallon)", "oilpriceapi-widgets"),
              value: attributes.basePrice || "2.50",
              onChange: function (value) {
                setAttributes({ basePrice: value });
              },
            }),

        ),
      );

      var previewBg = theme === "dark" ? "#0f172a" : "#f8fafc";
      var previewText = theme === "dark" ? "#ffffff" : "#0f172a";
      var previewAccent = theme === "dark" ? "#f97316" : "#ea580c";

      var content = el(
        "div",
        blockProps,
        inspectorControls,
        el(
          Placeholder,
          {
            icon: "chart-line",
            label:
              widgetLabels[widgetType] ||
              __("OilPriceAPI Widget", "oilpriceapi-widgets"),
            instructions: __(
              "The source date and weekly cadence appear with the data on the frontend.",
              "oilpriceapi-widgets",
            ),
          },
          el(
            "div",
            {
              style: {
                backgroundColor: previewBg,
                color: previewText,
                padding: "16px 20px",
                borderRadius: "8px",
                fontFamily: "system-ui, sans-serif",
                fontSize: "14px",
                width: "100%",
              },
            },
            el(
              "div",
              {
                style: {
                  color: previewAccent,
                  fontWeight: "600",
                  marginBottom: "8px",
                },
              },
              "OilPriceAPI",
            ),
            el(
              "div",
              null,
              widgetLabels[widgetType],
              " \u2022 ",
              theme.charAt(0).toUpperCase() + theme.slice(1) + " Theme",
            ),
          ),
        ),
      );

      return content;
    },

    save: function () {
      // Dynamic block — rendered by PHP on the server.
      return null;
    },
  });
})(window.wp);
