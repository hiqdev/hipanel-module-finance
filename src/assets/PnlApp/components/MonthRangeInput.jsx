import React, { useState } from "react";
import { DatePicker, Button, Space } from "antd";
import { CalculatorOutlined } from "@ant-design/icons";

const { RangePicker } = DatePicker;

const MonthRangeInput = ({ disabledDate, icon, label, handleCalculate, loading }) => {
  const [isDisabled, setDisabled] = useState(true);
  const [date, setDate] = useState();
  const handleMonthRangeChange = (date, dateString) => {
    if (dateString.indexOf("") === -1) {
      setDate({ date, dateString });
      setDisabled(false);
    } else {
      setDisabled(true);
    }
  };

  return (
    <Space style={{ marginBottom: "1em" }}>
      <RangePicker
        picker="month"
        onChange={handleMonthRangeChange}
        disabledDate={disabledDate}
        disabled={loading}
      />
      <Button
        type="primary"
        icon={icon}
        disabled={isDisabled}
        loading={loading}
        onClick={(event) => {
          handleCalculate(event, date);
        }}
      >
        {label}
      </Button>
    </Space>
  );
};

export default MonthRangeInput;
