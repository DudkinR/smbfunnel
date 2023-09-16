/*
Copyright 2017 Ziadin Givan

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

https://github.com/givanz/VvvebJs
*/

Vvveb.ComponentsGroup["Membership Page Variables"] = [
  "membership/update_user_info",
  "membership/forgot_password",
  "membership/shipping_detail",
  "membership/member_orders",
  "membership/member_order_elements",
  "membership/member_logout_element",
  "membership/member_fpwd_element",
  "membership/download_product",
];

Vvveb.Components.extend("_base", "membership/update_user_info", {
  name: t("Update detail"),
  image: "icons/variables/description.png",
  attribute: "data_user_detail_update_form",
  html: /*html*/ `<div
    data_user_detail_update_form
    class="container"
    style="
      padding-top: 4px;
      padding-bottom: 8px;
      margin-bottom: 5px;
      border: 4px;
      background-color: #f2f2f2;
    "
  >
    <form action="" method="POST">
      <h5>User information</h5>
      <div style="text-align: center; color: firebrick; font-weight: 600">
        {update_error_message}
      </div>
      <div style="text-align: center; color: #006600; font-weight: 600">
        {update_success_message}
      </div>
      <div class="mb-3">
        <label>Name</label>
        <input
          type="text"
          class="form-control"
          placeholder="Please enter your name"
          name="name"
        />
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input
          type="email"
          class="form-control"
          placeholder="Please enter your email"
          name="email"
        />
      </div>
      <button class="btn btn-primary btn-block" name="update_membership_info">Save</button>
    </form>
  </div>`,
});

Vvveb.Components.extend("_base", "membership/forgot_password", {
  name: t("Forgot password"),
  image: "icons/variables/forgot-password.png",
  attributes: ["data_fpwd_step"],
  html: /*html*/ `<section data_fpwd_step="insert_email">
    <div
      class="container"
      style="
        padding-top: 4px;
        padding-bottom: 8px;
        margin-bottom: 5px;
        border: 4px;
        background-color: #f2f2f2;
      "
    >
      <form action="" method="POST">
        <div
          style="
            padding: 4px;
            text-align: center;
            color: firebrick;
            font-weight: 600;
          "
        >
          {validation_error}
        </div>
        <div class="mb-3">
          <label>Enter Your Email Id</label>
          <input
            type="email"
            name="email"
            class="form-control"
            placeholder="Enter Your Email ID where the send the OTP"
          />
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          Send Reset Link
        </button>
      </form>
    </div>
  </section>
  
  <section data_fpwd_step="confirmation_message">
    <div
      class="container"
      style="
        padding-top: 4px;
        padding-bottom: 8px;
        margin-bottom: 5px;
        border: 4px;
        background-color: #f2f2f2;
      "
    >
      <div class="alert alert-info">
        <strong
          >We have sent an password reset link to the email id you provided,
          please visit.</strong
        >
      </div>
    </div>
  </section>
  
  <section data_fpwd_step="update_password">
    <div
      class="container"
      style="
        padding-top: 4px;
        padding-bottom: 8px;
        margin-bottom: 5px;
        border: 4px;
        background-color: #f2f2f2;
      "
    >
      <form action="" method="POST">
        <div
          style="
            padding: 4px;
            text-align: center;
            color: firebrick;
            font-weight: 600;
          "
        >
          {validation_error}
        </div>
        <div class="mb-3">
          <label>Enter New Password</label>
          <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Enter New Password"
          />
        </div>
  
        <div class="mb-3">
          <label class="lblreenter">Re-enter New Password</label>
          <input
            type="password"
            class="form-control"
            name="reenterpassword"
            placeholder="Re-enter Password"
          />
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          Update Password
        </button>
      </form>
    </div>
  </section>`,
  properties: [
    {
      name: t("Step"),
      key: "data_fpwd_step_key",
      htmlAttr: "data_fpwd_step",
      inputtype: TextInput,
    },
  ],
});

Vvveb.Components.extend("_base", "membership/shipping_detail", {
  name: t("Shipping detail"),
  key: "data_shipping_detail_key",
  image: "icons/variables/shiping-detail.png",
  htmlAttr: ["data_shipping_detail", "data_shipping_detail_loop"],
  html: /*html*/ `<section data_shipping_detail>
    <div
      class="container"
      style="
        padding-top: 4px;
        padding-bottom: 8px;
        margin-bottom: 5px;
        border: 4px;
        background-color: #f2f2f2;
      "
    >
      <div>
        <button
          data-bs-toggle-collapse-controller=""
          data-bs-target="#add_new_shipping_detail"
          aria-expanded="false"
          data-el-type="button"
          type="button"
          class="btn btn-primary"
        >
          <i class="fas fa-plus"></i> Add new address
        </button>
      </div>
  
      <div
        data-bs-toggle-collapse-area
        data-el-type="div"
        data_keep_area_visible="false"
        style="padding: 8px; margin-top: 4px"
        class="card card-body"
        id="add_new_shipping_detail"
      >
        <form action="" method="post">
          <div class="form-row">
            <div class="mb-3 col-md-6">
              <input
                type="text"
                class="form-control"
                id="inputEmail4"
                placeholder="First name"
                name="firstname"
              />
            </div>
            <div class="mb-3 col-md-6">
              <input
                type="text"
                class="form-control"
                placeholder="Last name"
                name="lastname"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="mb-3 col-md-6">
              <textarea
                type="text"
                class="form-control"
                id="inputAddress"
                placeholder="Address"
                name="shipping_address"
                sf-input-data-value=""
              ></textarea>
            </div>
            <div class="mb-3 col-md-6">
              <textarea
                type="text"
                class="form-control"
                id="inputAddress2"
                placeholder="Apartment, suite, etc. (optional)"
                name="shipping_address2"
                sf-input-data-value=""
              ></textarea>
            </div>
          </div>
  
          <div class="mb-3">
            <input
              placeholder="City"
              type="text"
              class="form-control"
              name="shipping_city"
              sf-input-data-value=""
            />
          </div>
  
          <div id="billing-address">
            <div class="form-row justify-content-between">
              <div class="mb-3 col-md-4">
                <input
                  type="text"
                  class="form-control h69"
                  placeholder="Country"
                  name="shipping_country"
                  sf-input-data-value=""
                />
              </div>
  
              <div class="mb-3 col-md-4">
                <input
                  type="text"
                  class="form-control h69"
                  placeholder="State"
                  name="shipping_state"
                  sf-input-data-value=""
                />
              </div>
  
              <div class="mb-3 col-md-4">
                <input
                  type="text"
                  class="form-control h69"
                  placeholder="PIN Code"
                  name="shipping_pincode"
                  sf-input-data-value=""
                />
              </div>
            </div>
          </div>
          <div class="mb-3" style="text-align: right">
            <label
              ><input
                type="checkbox"
                value="0"
                name="set_default_shipping_detail"
              />&nbsp;Make default shipping detail</label
            >&nbsp;<button
              name="update_member_shipping_detail"
              class="btn btn-primary"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
  
  <section data_shipping_detail_loop>
    <div
      class="container"
      style="
        padding-top: 4px;
        padding-bottom: 8px;
        margin-bottom: 5px;
        border: 4px;
        background-color: #f2f2f2;
      "
      data_shipping_address_count="{loop_count}"
    >
      <div class="card" style="border-radius: 0px">
        <div class="card-header">
          <div class="row">
            <div class="col-10">
              <p><strong>{name}</strong></p>
              <p>{shipping_city}, {shipping_state}, {shipping_country}</p>
              <p>{shipping_pincode}</p>
            </div>
            <div class="col-2" style="text-align: right">
              <button
                type="button"
                class="btn btn-default"
                data-bs-toggle-collapse-controller
                data-bs-target="#data_shipping_count_{loop_count}"
                aria-expanded="false"
                data-el-type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
            </div>
          </div>
        </div>
        <div
          id="data_shipping_count_{loop_count}"
          class="card-body"
          data-bs-toggle-collapse-area
          data-el-type="div"
          data_keep_area_visible="false"
        >
          <form action="" method="post">
            <div class="form-row">
              <div class="mb-3 col-md-6">
                <input
                  type="text"
                  class="form-control"
                  id="inputEmail4"
                  placeholder="First name"
                  name="firstname"
                />
              </div>
              <div class="mb-3 col-md-6">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Last name"
                  name="lastname"
                />
              </div>
            </div>
            <div class="form-row">
              <div class="mb-3 col-md-6">
                <textarea
                  type="text"
                  class="form-control"
                  id="inputAddress"
                  placeholder="Address"
                  name="shipping_address"
                  sf-input-data-value=""
                ></textarea>
              </div>
              <div class="mb-3 col-md-6">
                <textarea
                  type="text"
                  class="form-control"
                  id="inputAddress2"
                  placeholder="Apartment, suite, etc. (optional)"
                  name="shipping_address2"
                  sf-input-data-value=""
                ></textarea>
              </div>
            </div>
  
            <div class="mb-3">
              <input
                placeholder="City"
                type="text"
                class="form-control"
                name="shipping_city"
                sf-input-data-value=""
              />
            </div>
  
            <div id="billing-address">
              <div class="form-row justify-content-between">
                <div class="mb-3 col-md-4">
                  <input
                    type="text"
                    class="form-control h69"
                    placeholder="Country"
                    name="shipping_country"
                    sf-input-data-value=""
                  />
                </div>
  
                <div class="mb-3 col-md-4">
                  <input
                    type="text"
                    class="form-control h69"
                    placeholder="State"
                    name="shipping_state"
                    sf-input-data-value=""
                  />
                </div>
  
                <div class="mb-3 col-md-4">
                  <input
                    type="text"
                    class="form-control h69"
                    placeholder="PIN Code"
                    name="shipping_pincode"
                    sf-input-data-value=""
                  />
                </div>
              </div>
            </div>
            <div class="mb-3" style="text-align: right">
              <label
                ><input
                  type="checkbox"
                  name="set_default_shipping_detail"
                  value="{shipping_id}"
                />&nbsp;Make default shipping detail</label
              >&nbsp;<button
                class="btn btn-light"
                name="delete_shipping_detail"
                value="{shipping_id}"
              >
                Delete</button
              >&nbsp;<button
                name="update_member_shipping_detail"
                class="btn btn-primary"
                value="{shipping_id}"
              >
                Save
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>`,
});

Vvveb.Components.extend("_base", "membership/member_orders", {
  name: t("Orders"),
  key: "data_member_order_detail_key",
  image: "icons/variables/orders.png",
  htmlAttr: "data_member_order_detail",
  html: /*html*/ `<section data_member_order_detail>
  <div class="container" style="margin-bottom: 4px">
    <div class="card card-body" style="background-color: #f2f2f2">
      <div class="row">
        <div class="col-sm-2" style="padding: 0px 24px; margin-bottom: 10px">
          <div
            sf-data-member-product-el
            sf-product-media-container="1"
            sf-media-single="1"
            sf-element-data-html="{product.media}"
          >
            <img class="img-fluid" src="${cf_editor_url}/libs/builder/icons/variables/image.png"/>
          </div>
        </div>
        <div class="col-sm-7" style="margin-bottom: 12px">
          <h4
            sf-data-member-product-el
            sf-element-data-html="{product.title}"
            style="font-size: 22px; color: #4f4d4d"
          >
            This is the product title
          </h4>
          <h4 style="font-size: 15px; font-weight: 600; color: #666666">
            Order id: #<span
              sf-data-member-product-el
              sf-element-data-html="{product.purchase.payment_id}"
              >GD4587H56</span
            >
          </h4>
          <p style="font-size: 15px; color: #666666; margin-bottom: 1px;">
            Quantity:
            <span
              sf-data-member-product-el
              sf-element-data-html="{product.purchase.quantity}"
              >10</span
            >
          </p>
          <p style="font-size: 15px; color: #666666; margin-top: 1px">
            Price:
            <span
              sf-data-member-product-el
              sf-element-data-html="{product.purchase.amount}"
              >10</span
            >&nbsp;<span 
            sf-data-member-product-el
            sf-element-data-html="{product.currency}"
            >USD</span>
          </p>
          <a
            sf-data-member-product-el
            class="btn btn-warning"
            href="{product.url}"
            style="font-size: 15px"
          >
            <i class="fas fa-shopping-cart"></i>&nbsp;Buy again</a
          >
        </div>
        <div class="col-sm-3" style="margin-bottom: 4px">
          <div class="row">
            <div class="col-sm-12">
              <p style="font-size: 15px; color: #666666; margin-bottom: 5px">
                Placed on:
                <span
                  sf-data-member-product-el
                  sf-element-data-html="{product.purchase.date}"
                  >18-Apr-2022</span
                >
              </p>
              <p style="font-size: 14px; color: #666666; margin-bottom: 5px">
                <span style="font-weight: 400">Active:</span>
                <span
                  sf-data-member-product-el
                  data-member-order-active="{product.purchase.valid}"
                  ><i class="fas fa-check-circle" style="color: #2d8659"></i
                ></span>
              </p>
              <p style="font-size: 14px; color: #666666">
                <span style="font-weight: 400">Shipped:</span>
                <span
                  sf-data-member-product-el
                  data-member-order-shipped="{product.purchase.shipped}"
                  ><i class="fas fa-check-circle" style="color: #2d8659"></i
                ></span>
              </p>
            </div>

            <div class="col-sm-12" style="margin-top: 2px">
              <a
                sf-data-member-product-el
                class="btn btn-outline-warning"
                href="{product.review_url}"
                style="font-size: 15px"
                ><i class="fas fa-star"></i>&nbsp;Write a review</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>`,
});
Vvveb.Components.extend("_base", "membership/download_product",{
  name: t("Download Product Button"),
  image: "icons/button.svg",
  attributes: ["sf_order_download"],
  html: `<a href="{product.download_url}" download sf_order_download="1" class="btn btn-success btn-lg mt-30 light">
    Download</a>`,
});
Vvveb.Components.extend("_base", "membership/member_order_elements", {
  name: t("Product Elements"),
  image: "icons/variables/product-elements.png",
  key: "data_member_order_element_key",
  attributes: ["sf-data-member-product-el"],
  html: /*html*/ `<span sf-data-member-product-el data-el-type="span">Data</span>`,
  properties: [
    {
      name: t("Element type"),
      key: "member_order_element_type_key",
      htmlAttr: "data-el-type",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_ELEMENT_TYPES,
      },
    },
    {
      name: t("Element input value"),
      key: "member_order_element_input_value",
      htmlAttr: "sf-input-data-value",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_MEMBER_PRODUCT_ATTRIBUTES,
      },
    },
    {
      name: t("Element html value"),
      key: "member_order_element_html_value",
      htmlAttr: "sf-element-data-html",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_MEMBER_PRODUCT_ATTRIBUTES,
      },
    },
  ],
  onChange: function (node, property, type) {
    if (property.key === "member_order_element_type_key") {
      let newEl = $(`<${type}></${type}>`);
      $.each(node[0].attributes, (index, attribute) => {
        let key = attribute.name;
        let val = attribute.value;
        if (key === "href" && type !== "a") {
          return;
        }
        if (type === "a") {
          newEl.attr("href", "#");
        }
        newEl.attr(key, val);
      });

      let html = $(node[0]).html();
      newEl.html(html);
      node.replaceWith(newEl);
    }
  },
});

Vvveb.Components.extend("_base", "membership/member_logout_element", {
  name: t("Logout Element"),
  image: "icons/variables/logout.png",
  key: "data_member_logout_element",
  attributes: ["data-member-logout-el"],
  html: `<a href="{logout_url}" data-member-logout-el="1" data-logout-el-type="a">Logout</a>`,
  properties: [
    {
      name: t("Element type"),
      key: "member_data_logout_el",
      htmlAttr: "data-logout-el-type",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_ELEMENT_TYPES,
      },
    },
  ],
  onChange: function (node, property, type) {
    if (property.key === "member_data_logout_el") {
      let newEl = $(`<${type}></${type}>`);
      $.each(node[0].attributes, (index, attribute) => {
        let key = attribute.name;
        let val = attribute.value;
        if (key === "href" && type !== "a") {
          return;
        }
        newEl.attr(key, val);
      });

      if (type === "a") {
        newEl.attr("href", "{logout_url}");
      } else {
        newEl.attr("data-autolink-url", "{logout_url}");
        newEl.attr(`data-autolink`, 1);
      }

      let html = $(node[0]).html();
      newEl.html(html);
      node.replaceWith(newEl);
    }
  },
});

Vvveb.Components.extend("_base", "membership/member_fpwd_element", {
  name: t("Forgot password link"),
  image: "icons/variables/fpwd_link.png",
  key: "data_member_fpwd_element",
  attributes: ["data-member-fpwd-el"],
  html: `<a href="{fpwd_url}" data-member-fpwd-el="1" data-fpwd-el-type="a">Reset password</a>`,
  properties: [
    {
      name: t("Element type"),
      key: "member_data_fpwd_el",
      htmlAttr: "data-fpwd-el-type",
      inputtype: SelectInput,
      data: {
        options: CF_GLOBAL_ELEMENT_TYPES,
      },
    },
  ],
  onChange: function (node, property, type) {
    if (property.key === "member_data_fpwd_el") {
      console.log(`Selected type >> `, type);
      let newEl = $(`<${type}></${type}>`);
      $.each(node[0].attributes, (index, attribute) => {
        let key = attribute.name;
        let val = attribute.value;
        if (key === "href" && type !== "a") {
          return;
        }
        newEl.attr(key, val);
      });

      if (type === "a") {
        newEl.attr("href", "{fpwd_url}");
      } else {
        newEl.attr("data-autolink-url", "{fpwd_url}");
        newEl.attr(`data-autolink`, 1);
      }

      let html = $(node[0]).html();
      newEl.html(html);
      node.replaceWith(newEl);
    }
  },
});
