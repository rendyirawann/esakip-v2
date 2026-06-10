<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipEselon;

/**
 * SakipEselonSearch represents the model behind the search form of `backend\models\SakipEselon`.
 */
class SakipEselonSearch extends SakipEselon
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refeselon_id'], 'integer'],
            [['title_eselon'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipEselon::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refeselon_id' => $this->refeselon_id,
        ]);

        $query->andFilterWhere(['like', 'title_eselon', $this->title_eselon]);

        return $dataProvider;
    }
}
