(function (wp) {
  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;
  var ToggleControl = wp.components.ToggleControl;
  var TextControl = wp.components.TextControl;
  var Placeholder = wp.components.Placeholder;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var __ = wp.i18n.__;

  var widgetLabels = {
    ticker: __("Oil Price Ticker", "oilpriceapi-widget"),
    diesel: __("Diesel Price Tracker", "oilpriceapi-widget"),
    "fuel-surcharge": __("Fuel Surcharge Calculator", "oilpriceapi-widget"),
    carbon: __("Carbon Cost Calculator", "oilpriceapi-widget"),
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
            title: __("Widget Settings", "oilpriceapi-widget"),
            initialOpen: true,
          },
          el(SelectControl, {
            label: __("Widget Type", "oilpriceapi-widget"),
            value: widgetType,
            options: [
              {
                label: __("Oil Price Ticker", "oilpriceapi-widget"),
                value: "ticker",
              },
              {
                label: __("Diesel Price Tracker", "oilpriceapi-widget"),
                value: "diesel",
              },
              {
                label: __("Fuel Surcharge Calculator", "oilpriceapi-widget"),
                value: "fuel-surcharge",
              },
              {
                label: __("Carbon Cost Calculator", "oilpriceapi-widget"),
                value: "carbon",
              },
            ],
            onChange: function (value) {
              setAttributes({ widgetType: value });
            },
          }),
          el(SelectControl, {
            label: __("Theme", "oilpriceapi-widget"),
            value: theme,
            options: [
              { label: __("Dark", "oilpriceapi-widget"), value: "dark" },
              { label: __("Light", "oilpriceapi-widget"), value: "light" },
            ],
            onChange: function (value) {
              setAttributes({ theme: value });
            },
          }),

          // Ticker-specific options.
          widgetType === "ticker" &&
            el(SelectControl, {
              label: __("Layout", "oilpriceapi-widget"),
              value: attributes.layout || "horizontal",
              options: [
                {
                  label: __("Horizontal", "oilpriceapi-widget"),
                  value: "horizontal",
                },
                {
                  label: __("Vertical", "oilpriceapi-widget"),
                  value: "vertical",
                },
              ],
              onChange: function (value) {
                setAttributes({ layout: value });
              },
            }),
          widgetType === "ticker" &&
            el(TextControl, {
              label: __("Commodities", "oilpriceapi-widget"),
              help: __(
                "Comma-separated: BRENT, WTI, NATGAS",
                "oilpriceapi-widget",
              ),
              value: attributes.commodities || "BRENT,WTI",
              onChange: function (value) {
                setAttributes({ commodities: value });
              },
            }),

          // Diesel-specific options.
          widgetType === "diesel" &&
            el(ToggleControl, {
              label: __("Show Regional Prices", "oilpriceapi-widget"),
              checked: attributes.regional !== "false",
              onChange: function (value) {
                setAttributes({ regional: value ? "true" : "false" });
              },
            }),

          // Fuel surcharge-specific options.
          widgetType === "fuel-surcharge" &&
            el(TextControl, {
              label: __("Base Fuel Price ($/gallon)", "oilpriceapi-widget"),
              value: attributes.basePrice || "2.50",
              onChange: function (value) {
                setAttributes({ basePrice: value });
              },
            }),

          // Carbon-specific options.
          widgetType === "carbon" &&
            el(TextControl, {
              label: __(
                "Carbon Price ($/tonne CO\u2082)",
                "oilpriceapi-widget",
              ),
              value: attributes.carbonPrice || "50",
              onChange: function (value) {
                setAttributes({ carbonPrice: value });
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
              __("OilPriceAPI Widget", "oilpriceapi-widget"),
            instructions: __(
              "This widget will display live data on the frontend. Configure options in the sidebar.",
              "oilpriceapi-widget",
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
